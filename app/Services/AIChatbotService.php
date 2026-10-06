<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class AIChatbotService
{
    /**
     * Phân tích câu hỏi của khách hàng và trả về câu trả lời tư vấn chuyên sâu,
     * kèm danh sách sản phẩm gợi ý và nút thao tác nhanh (Quick Replies).
     */
    public function respond(string $message, array $history = []): array
    {
        $rawMessage = trim($message);
        $normalized = $this->normalizeText($rawMessage);

        // 1. Kiểm tra nếu khách hỏi về tính giá / kích thước may rèm (VD: 2.5m x 2.8m, 3m x 2m)
        $dimensionMatch = $this->extractDimensions($rawMessage);
        if ($dimensionMatch) {
            return $this->handlePriceCalculation($dimensionMatch, $normalized);
        }

        // 2. Khảo sát / Đo đạc tại nhà
        if (Str::contains($normalized, ['do nha', 'khao sat', 'tan nha', 'hen lich', 'dat lich', 'do dac', 'tho den'])) {
            return $this->handleHomeSurveyIntent();
        }

        // 3. Tư vấn theo Hướng Nhà / Nắng gắt
        if (Str::contains($normalized, ['huong tay', 'nang gat', 'chong nang', 'can sang', 'chong tia uv', 'nong qua', 'cach nhiet'])) {
            return $this->handleSunlightIntent();
        }

        // 4. Tư vấn theo Loại Phòng: Phòng khách
        if (Str::contains($normalized, ['phong khach', 'phong sinh hoat', 'khach'])) {
            return $this->handleLivingRoomIntent();
        }

        // 5. Tư vấn theo Loại Phòng: Phòng ngủ
        if (Str::contains($normalized, ['phong ngu', 'ngu', 'phong em be', 'phong tre em'])) {
            return $this->handleBedroomIntent();
        }

        // 6. Tư vấn theo Loại Phòng: Văn phòng / Phòng làm việc / Cửa sổ nhỏ
        if (Str::contains($normalized, ['van phong', 'cong ty', 'phong lam viec', 'cua so nho', 'bep', 'nha bep', 'ban cong'])) {
            return $this->handleOfficeKitchenIntent();
        }

        // 7. Tư vấn theo Loại Rèm Cụ Thể: Cầu vồng Hàn Quốc
        if (Str::contains($normalized, ['cau vong', 'han quoc', 'modero', 'combo'])) {
            return $this->handleRainbowCurtainIntent();
        }

        // 8. Tư vấn theo Loại Rèm: Rèm vải 2 lớp / Vải gấm / Vải voan
        if (Str::contains($normalized, ['rem vai', '2 lop', 'hai lop', 'gam', 'voan', 'nhung', 'tho'])) {
            return $this->handleFabricCurtainIntent();
        }

        // 9. Tư vấn theo Loại Rèm: Rèm gỗ / Rèm sáo
        if (Str::contains($normalized, ['rem go', 'sao go', 'la doc', 'sao nhom', 'nhom'])) {
            return $this->handleWoodenCurtainIntent();
        }

        // 10. Tư vấn Rèm Cuốn
        if (Str::contains($normalized, ['rem cuon', 'cuon tron', 'cuon van phong'])) {
            return $this->handleRollerCurtainIntent();
        }

        // 11. Khuyến mãi / Giảm giá / Voucher
        if (Str::contains($normalized, ['khuyen mai', 'giam gia', 'voucher', 'ma giam', 'sale', 'uu dai', 'voucher'])) {
            return $this->handlePromotionIntent();
        }

        // 12. Chính sách / Bảo hành / Vận chuyển
        if (Str::contains($normalized, ['bao hanh', 'doi tra', 'van chuyen', 'ship', 'lap dat', 'phi ship'])) {
            return $this->handlePolicyIntent();
        }

        // 13. Chào hỏi ban đầu hoặc Fallback tổng quát
        return $this->handleGeneralGreetingOrFallback($rawMessage, $normalized);
    }

    /**
     * Trích xuất kích thước từ câu hỏi: ví dụ "rộng 2.5m cao 2.8m", "2m x 2.6m", "3m2", v.v.
     */
    protected function extractDimensions(string $text): ?array
    {
        // Dạng 1: "2.5m x 2.8m" hoặc "2.5 x 2.8" hoặc "2m x 3m" hoặc "2.5m * 2.8m"
        if (preg_match('/([0-9]+[.,]?[0-9]*)\s*(?:m|met)?\s*(?:x|\*|nhan|\bx\b)\s*([0-9]+[.,]?[0-9]*)\s*(?:m|met)?/i', $text, $matches)) {
            $w = (float) str_replace(',', '.', $matches[1]);
            $h = (float) str_replace(',', '.', $matches[2]);
            if ($w > 0 && $h > 0 && $w <= 20 && $h <= 20) {
                return ['width' => $w, 'height' => $h, 'area' => round($w * $h, 2)];
            }
        }

        // Dạng 2: "rộng 2.5m cao 2.8m" hoặc "cao 2.8m rộng 2.5m"
        $width = null;
        $height = null;
        if (preg_match('/(?:rong|ngang)\s*[:=]?\s*([0-9]+[.,]?[0-9]*)\s*(?:m|met)?/i', $text, $wMatch)) {
            $width = (float) str_replace(',', '.', $wMatch[1]);
        }
        if (preg_match('/(?:cao|dai)\s*[:=]?\s*([0-9]+[.,]?[0-9]*)\s*(?:m|met)?/i', $text, $hMatch)) {
            $height = (float) str_replace(',', '.', $hMatch[1]);
        }

        if ($width && $height && $width <= 20 && $height <= 20) {
            return ['width' => $width, 'height' => $height, 'area' => round($width * $height, 2)];
        }

        // Dạng 3: "diện tích 6m2" hoặc "6m2"
        if (preg_match('/([0-9]+[.,]?[0-9]*)\s*(?:m2|m²|met vuong)/i', $text, $areaMatch)) {
            $area = (float) str_replace(',', '.', $areaMatch[1]);
            if ($area > 0 && $area <= 100) {
                return ['width' => null, 'height' => null, 'area' => $area];
            }
        }

        return null;
    }

    /**
     * Xử lý tính dự toán chi phí may rèm theo kích thước cửa thực tế
     */
    protected function handlePriceCalculation(array $dim, string $normalized): array
    {
        $area = $dim['area'];
        $dimText = ($dim['width'] && $dim['height']) 
            ? "Rộng {$dim['width']}m × Cao {$dim['height']}m (Diện tích: **{$area} m²**)" 
            : "Diện tích: **{$area} m²**";

        // Lấy sản phẩm tiêu biểu để báo giá
        $products = Product::where('stock_status', 'in_stock')
            ->orderBy('sold_count', 'desc')
            ->take(3)
            ->get();

        $reply = "📐 **BÁO GIÁ DỰ TOÁN MAY RÈM THEO KÍCH THƯỚC CỦA BẠN:**\n\n";
        $reply .= "• **Thông số cửa:** {$dimText}\n\n";
        $reply .= "📊 **Dự toán chi phí theo từng phân khúc rèm:**\n";

        // 1. Rèm cuốn văn phòng / chống nắng (giá khoảng 280k - 380k/m2)
        $rollerEst = number_format($area * 320000, 0, ',', '.');
        $reply .= "1. 🪟 **Rèm cuốn cản sáng 100%:** ~ **{$rollerEst} đ** *(Đơn giá ~320.000đ/m²)*\n";

        // 2. Rèm cầu vồng Hàn Quốc (giá khoảng 450k - 680k/m2)
        $rainbowEst = number_format($area * 520000, 0, ',', '.');
        $reply .= "2. 🌈 **Rèm Cầu Vồng Hàn Quốc:** ~ **{$rainbowEst} đ** *(Đơn giá ~520.000đ/m²)*\n";

        // 3. Rèm vải cao cấp 2 lớp Gấm + Voan (giá khoảng 650k - 850k/m2)
        $fabricEst = number_format($area * 680000, 0, ',', '.');
        $reply .= "3. 👑 **Rèm Vải 2 Lớp (Gấm + Voan):** ~ **{$fabricEst} đ** *(Đơn giá ~680.000đ/m²)*\n\n";

        $reply .= "🎁 **ƯU ĐÃI ĐẶC BIỆT KÈM THEO:**\n";
        $reply .= "✅ Miễn phí trọn bộ phụ kiện thanh ray nhôm đúc & công may định hình.\n";
        $reply .= "✅ Thợ mang mẫu vải đến tận nhà tư vấn & đo đạc chuẩn xác 100% hoàn toàn miễn phí!\n";
        $reply .= "✅ Bảo hành độ bền màu và phụ kiện lên đến **3 năm**.";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📅 Đặt lịch đo tại nhà miễn phí',
                '🌈 Xem mẫu rèm cầu vồng',
                '👑 Xem mẫu rèm vải 2 lớp',
                '🎁 Lấy mã giảm giá 50k'
            ]
        ];
    }

    /**
     * Hướng nắng gắt / Hướng Tây
     */
    protected function handleSunlightIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where(function ($q) {
                $q->where('name', 'like', '%cản sáng%')
                  ->orWhere('name', 'like', '%chống nắng%')
                  ->orWhere('name', 'like', '%cuốn%')
                  ->orWhere('name', 'like', '%cao cấp%');
            })
            ->take(3)
            ->get();

        $reply = "☀️ **TƯ VẤN RÈM CHO PHÒNG HƯỚNG TÂY & NẮNG GẮT:**\n\n";
        $reply .= "Đối với phòng bị nắng chiếu trực tiếp nhiệt độ cao, **Rèm Online** khuyên bạn nên lựa chọn 1 trong 2 giải pháp tối ưu sau:\n\n";
        $reply .= "1. 👑 **Rèm Vải 2 Lớp cản nhiệt:** Lớp ngoài là vải gấm dệt 3 lớp (hoặc vải tráng silicon cách nhiệt 100%), lớp trong là voan mềm lấy sáng dịu mát. Giúp phòng giảm ngay 3 - 5°C, tiết kiệm điện điều hòa.\n";
        $reply .= "2. 🪟 **Rèm Cuốn Blackout hoặc Rèm Cầu Vồng cản sáng 100%:** Chất liệu 100% Polyester phủ nhựa PVC chống tia UV, không hấp thụ nhiệt và chống phai màu nội thất.\n\n";
        $reply .= "💡 *Bạn có thể bấm vào các mẫu gợi ý bên dưới để xem màu vải hoặc nhập kích thước cửa (VD: 'rộng 2m cao 2.5m') để mình tính giá dự toán nhé!*";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📏 Báo giá rèm cửa 2.2m x 2.6m',
                '👑 Rèm vải 2 lớp phòng khách',
                '📅 Đặt lịch thợ mang mẫu đến xem',
            ]
        ];
    }

    /**
     * Tư vấn Phòng khách
     */
    protected function handleLivingRoomIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where(function ($q) {
                $q->where('name', 'like', '%vải%')
                  ->orWhere('name', 'like', '%2 lớp%')
                  ->orWhere('name', 'like', '%gấm%')
                  ->orWhere('name', 'like', '%cầu vồng%');
            })
            ->take(3)
            ->get();

        $reply = "🛋️ **TƯ VẤN RÈM CHO PHÒNG KHÁCH SANG TRỌNG:**\n\n";
        $reply .= "Phòng khách là bộ mặt của ngôi nhà, đòi hỏi tính thẩm mỹ cao và sự thông thoáng:\n\n";
        $reply .= "✨ **Lựa chọn số 1:** **Rèm Vải 2 Lớp (Gấm dệt + Voan trắng/thêu họa tiết)**.\n";
        $reply .= "• Tạo sóng rèm suông mềm mại từ trần xuống sàn, giúp căn phòng cao và rộng hơn.\n";
        $reply .= "• Ban ngày kéo lớp voan để đón ánh sáng tự nhiên dịu nhẹ; buổi tối kéo kín lớp gấm để tạo sự riêng tư.\n\n";
        $reply .= "✨ **Lựa chọn số 2:** **Rèm Cầu Vồng Hàn Quốc** hiện đại, gọn gàng, phù hợp chung cư và nhà phố phong cách tối giản (Minimalism).\n\n";
        $reply .= "🎨 *Gợi ý màu sắc:* Tone màu Be, Xám ghi, Nâu cafe hoặc Xanh pastel đang là xu hướng được ưa chuộng nhất 2026!";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📏 Tính giá rèm cửa 3m x 2.7m',
                '🌈 Xem rèm cầu vồng phòng khách',
                '📅 Hẹn thợ mang bảng màu vải đến nhà'
            ]
        ];
    }

    /**
     * Tư vấn Phòng ngủ
     */
    protected function handleBedroomIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where(function ($q) {
                $q->where('name', 'like', '%ngủ%')
                  ->orWhere('name', 'like', '%cản sáng%')
                  ->orWhere('name', 'like', '%roman%')
                  ->orWhere('name', 'like', '%cầu vồng%');
            })
            ->take(3)
            ->get();

        $reply = "🛏️ **TƯ VẤN RÈM CHO PHÒNG NGỦ YÊN TĨNH & ÊM ÁI:**\n\n";
        $reply .= "Tiêu chí hàng đầu cho phòng ngủ là **cản sáng 100% (Blackout)** và **cách âm, giữ nhiệt điều hòa** để bạn có giấc ngủ sâu trọn vẹn:\n\n";
        $reply .= "🌙 **Rèm vải cản sáng 100% (Vải Nhật / Hàn dệt sợi đen):** Chắn triệt để ánh đèn đường và ánh nắng sớm mai.\n";
        $reply .= "🌙 **Rèm Roman xếp lớp:** Cực kỳ gọn gàng cho các ô cửa sổ nhỏ đầu giường, kéo lên xếp nếp thẩm mỹ cao.\n";
        $reply .= "🌙 **Rèm Cầu Vồng Blackout:** Dễ dàng điều chỉnh khe sáng chỉ với một thao tác kéo nhẹ.\n\n";
        $reply .= "💬 *Bạn muốn may rèm cho cửa lớn ban công hay cửa sổ phòng ngủ? Nhắn cho mình kích thước nhé!*";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📏 Báo giá rèm phòng ngủ 2m x 2.2m',
                '🪟 Xem mẫu rèm Roman xếp lớp',
                '📅 Đặt lịch đo tận phòng ngủ'
            ]
        ];
    }

    /**
     * Tư vấn Văn phòng / Bếp / Cửa sổ nhỏ
     */
    protected function handleOfficeKitchenIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where(function ($q) {
                $q->where('name', 'like', '%gỗ%')
                  ->orWhere('name', 'like', '%cuốn%')
                  ->orWhere('name', 'like', '%sáo%');
            })
            ->take(3)
            ->get();

        $reply = "💼 **TƯ VẤN RÈM VĂN PHÒNG, PHÒNG LÀM VIỆC & CỬA SỔ NHỎ:**\n\n";
        $reply .= "Đối với không gian làm việc hoặc cửa sổ phòng bếp:\n\n";
        $reply .= "1. 🪵 **Rèm Sáo Gỗ tự nhiên (Gỗ Basswood / Thông tuyết):** Xoay lật lá 180 độ điều chỉnh luồng gió và ánh sáng, mang lại vẻ sang trọng, ấm cúng và bề thế cho bàn làm việc.\n";
        $reply .= "2. 🏢 **Rèm Cuốn trơn chống bám bụi:** Độ bền cực cao, dễ lau chùi, giá thành tiết kiệm, phù hợp văn phòng công ty.\n";
        $reply .= "3. 🛡️ **Rèm Sáo Nhôm / Nhựa giả gỗ:** Kháng nước tuyệt đối cho khu vực bồn rửa bát và phòng tắm.";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '🪵 Xem bảng giá rèm sáo gỗ',
                '🏢 Xem mẫu rèm cuốn văn phòng',
                '📅 Báo giá công trình số lượng lớn'
            ]
        ];
    }

    /**
     * Rèm cầu vồng Hàn Quốc
     */
    protected function handleRainbowCurtainIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where('name', 'like', '%cầu vồng%')
            ->take(3)
            ->get();

        $reply = "🌈 **RÈM CẦU VỒNG HÀN QUỐC CAO CẤP (COMBIS BLINDS):**\n\n";
        $reply .= "Rèm cầu vồng là dòng sản phẩm bán chạy nhất hiện nay với thiết kế 2 lớp vải dệt xen kẽ giữa vải cản sáng và lưới xuyên sáng:\n\n";
        $reply .= "✅ **Ưu điểm vượt trội:**\n";
        $reply .= "• Điều chỉnh ánh sáng linh hoạt 0% - 100% mà không cần kéo hết rèm lên.\n";
        $reply .= "• Hộp máng nhôm sơn tĩnh điện đồng màu cao cấp, chống đứt dây và vận hành êm ái.\n";
        $reply .= "• Vải dệt 100% Polyester kháng khuẩn, không bám bụi, độ bền trên 7 năm.\n\n";
        $reply .= "💰 **Đơn giá:** Dao động từ **390.000đ - 750.000đ/m²** tùy theo độ dày và mã vải.";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📏 Tính giá cửa 2m x 2.4m',
                '🎨 Xem bảng mã màu rèm cầu vồng',
                '📅 Hẹn mang mẫu rèm cầu vồng đến nhà'
            ]
        ];
    }

    /**
     * Rèm vải 2 lớp
     */
    protected function handleFabricCurtainIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where(function ($q) {
                $q->where('name', 'like', '%vải%')->orWhere('name', 'like', '%gấm%');
            })
            ->take(3)
            ->get();

        $reply = "👑 **RÈM VẢI CAO CẤP MAY THEO YÊU CẦU:**\n\n";
        $reply .= "Rèm vải tại **Rèm Online** được may đo thủ công tinh xảo với độ nhún chuẩn 2.5 - 2.8 lần vải:\n\n";
        $reply .= "• **Lớp vải chính:** Vải gấm dệt chìm, vải thô Bỉ, vải Linen tự nhiên hoặc vải Nhung tuyết cản sáng 90-100%.\n";
        $reply .= "• **Lớp voan:** Voan trắng xước tuyết, voan thêu tay nghệ thuật tạo nét mềm mại bay bổng.\n";
        $reply .= "• **Thanh ray:** Ray bi nhôm định hình chuẩn âm trần giảm âm êm ái.\n\n";
        $reply .= "🎁 *Tặng kèm đai rèm, núm vén hợp kim và miễn phí công may!*";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📏 Báo giá rèm vải 3.5m x 2.8m',
                '🛋️ Rèm vải phòng khách',
                '📅 Thợ mang cây vải mẫu đến nhà'
            ]
        ];
    }

    /**
     * Rèm gỗ / Rèm sáo
     */
    protected function handleWoodenCurtainIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where(function ($q) {
                $q->where('name', 'like', '%gỗ%')->orWhere('name', 'like', '%sáo%');
            })
            ->take(3)
            ->get();

        $reply = "🪵 **RÈM SÁO GỖ TỰ NHIÊN 100%:**\n\n";
        $reply .= "Chất liệu gỗ tự nhiên nhập khẩu (Gỗ Sồi Nga, Gỗ Bách Hương, Gỗ Basswood) đã qua xử lý sấy nhiệt chống cong vênh, mối mọt và phủ sơn UV chống tia cực tím.\n\n";
        $reply .= "• Bản lá rộng 35mm - 50mm sang trọng.\n• Dây kéo sợi dù chịu lực cao cấp.\n• Bảo hành chính hãng 3 năm.";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📏 Tính giá rèm gỗ 1.8m x 2m',
                '💼 Rèm gỗ cho phòng làm việc',
                '📅 Đặt lịch khảo sát'
            ]
        ];
    }

    /**
     * Rèm cuốn
     */
    protected function handleRollerCurtainIntent(): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->where('name', 'like', '%cuốn%')
            ->take(3)
            ->get();

        $reply = "🪟 **RÈM CUỐN CHỐNG NẮNG CÁCH NHIỆT (ROLLER BLINDS):**\n\n";
        $reply .= "Giải pháp tối ưu chi phí, thẩm mỹ gọn gàng số 1 cho cửa kính lớn và văn phòng:\n\n";
        $reply .= "• Cản nắng 100%, cản nhiệt 95%.\n• Giá chỉ từ **280.000đ - 380.000đ/m²** hoàn thiện trọn gói.\n• Dễ vệ sinh bằng khăn ẩm.";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '📏 Báo giá rèm cuốn 5 cửa sổ',
                '🎁 Xem voucher giảm giá',
                '📅 Đặt lịch đo miễn phí'
            ]
        ];
    }

    /**
     * Khảo sát & Đo đạc tận nhà
     */
    protected function handleHomeSurveyIntent(): array
    {
        $reply = "🏡 **DỊCH VỤ MANG MẪU VẢI & ĐO ĐẠC TẬN NHÀ MIỄN PHÍ:**\n\n";
        $reply .= "Bạn đang băn khoăn về kích thước cửa hay muốn trực tiếp sờ chất vải thực tế dưới ánh sáng ngôi nhà mình?\n\n";
        $reply .= "✨ **Quy trình 3 bước tiện lợi:**\n";
        $reply .= "1. Kỹ thuật viên Rèm Online liên hệ xác nhận thời gian phù hợp (trong ngày hoặc cuối tuần).\n";
        $reply .= "2. Mang đầy đủ quyển mẫu vải (hơn 500+ mẫu vải gấm, voan, cầu vồng, gỗ) đến tận nhà bạn.\n";
        $reply .= "3. Đo đạc bằng máy laser chuẩn từng milimet, tư vấn kiểu may lọt lòng/phủ bì và lập bảng báo giá chi tiết hoàn toàn miễn phí!\n\n";
        $reply .= "👉 *Bạn có thể nhấn vào nút **'Đặt Lịch Đo Tại Nhà'** bên dưới để đăng ký ngay chỉ trong 30 giây nhé!*";

        return [
            'reply' => $reply,
            'products' => [],
            'quick_replies' => [
                '📅 Đặt Lịch Đo Tận Nhà Ngay',
                '📏 Tính giá rèm cửa 2.5m x 2.8m',
                '👑 Xem mẫu rèm vải 2 lớp',
                '🌈 Xem mẫu rèm cầu vồng'
            ]
        ];
    }

    /**
     * Khuyến mãi / Voucher
     */
    protected function handlePromotionIntent(): array
    {
        $reply = "🔥 **CÁC CHƯƠNG TRÌNH KHUYẾN MÃI & VOUCHER ĐANG ÁP DỤNG:**\n\n";
        $reply .= "🎁 **REMMOI50K:** Giảm ngay **50.000 VNĐ** cho đơn hàng đầu tiên từ 500k.\n";
        $reply .= "🎁 **VIPREM10:** Giảm **10%** tổng giá trị đơn hàng may rèm toàn nhà (tối đa 300k).\n";
        $reply .= "🎁 **GIAM100K:** Giảm ngay **100.000 VNĐ** cho đơn hàng từ 2.000.000đ.\n\n";
        $reply .= "🚀 *Đặc biệt: Miễn phí toàn bộ phụ kiện thanh ray định hình và công lắp đặt khảo sát tận nơi!*";

        return [
            'reply' => $reply,
            'products' => [],
            'quick_replies' => [
                '🛒 Mua sắm & Áp dụng voucher',
                '📏 Tính giá may rèm',
                '📅 Đặt lịch đo tận nhà'
            ]
        ];
    }

    /**
     * Chính sách & Bảo hành
     */
    protected function handlePolicyIntent(): array
    {
        $reply = "🛡️ **CHÍNH SÁCH BẢO HÀNH & GIAO HÀNG TẠI RÈM ONLINE:**\n\n";
        $reply .= "• **Bảo hành:** Cam kết bảo hành chính hãng **3 năm** cho thanh phụ kiện, động cơ tự động và bảo hành độ bền màu vải 2 năm.\n";
        $reply .= "• **Đổi trả:** 1 đổi 1 trong vòng **7 ngày** nếu sai kích thước hoặc lỗi từ nhà sản xuất.\n";
        $reply .= "• **Vận chuyển:** Tích hợp giao hàng hỏa tốc toàn quốc qua **Giao Hàng Nhanh (GHN)** với mã vận đơn theo dõi 24/7.";

        return [
            'reply' => $reply,
            'products' => [],
            'quick_replies' => [
                '📏 Tính giá rèm cửa nhà bạn',
                '📅 Đặt lịch khảo sát miễn phí',
                '🔥 Xem mẫu khuyến mãi hot'
            ]
        ];
    }

    /**
     * Lời chào mặc định & FAQ
     */
    protected function handleGeneralGreetingOrFallback(string $rawMessage, string $normalized): array
    {
        $products = Product::where('stock_status', 'in_stock')
            ->orderBy('is_featured', 'desc')
            ->orderBy('sold_count', 'desc')
            ->take(3)
            ->get();

        $reply = "👋 **Xin chào bạn! Mình là Trợ Lý AI Tư Vấn May Đo Rèm Online (24/7).**\n\n";
        $reply .= "Mình có thể hỗ trợ bạn ngay lập tức:\n";
        $reply .= "1. 💡 **Tư vấn chọn mẫu rèm** phù hợp cho phòng khách, phòng ngủ, phòng hướng Tây nắng gắt.\n";
        $reply .= "2. 📐 **Tính giá may rèm tự động:** Bạn chỉ cần gõ kích thước (ví dụ: *'rộng 2.5m cao 2.8m rèm vải giá bao nhiêu?'*).\n";
        $reply .= "3. 🏡 **Hẹn lịch thợ đến tận nhà:** Mang 500+ mẫu vải thực tế đến tận nơi tư vấn miễn phí.\n\n";
        $reply .= "Bạn đang cần tìm rèm cho không gian phòng nào?";

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($products),
            'quick_replies' => [
                '🛋️ Tư vấn rèm phòng khách',
                '🛏️ Tư vấn rèm phòng ngủ',
                '☀️ Phòng hướng Tây nắng gắt',
                '📏 Tính giá cửa 2.5m x 2.8m',
                '📅 Đặt lịch thợ đến đo tại nhà'
            ]
        ];
    }

    /**
     * Định dạng dữ liệu sản phẩm trả về frontend
     */
    protected function formatProducts($products): array
    {
        $result = [];
        foreach ($products as $p) {
            $effectivePrice = $p->sale_price ?? $p->unit_price;
            $result[] = [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'url' => route('products.show', $p->slug),
                'image_url' => $p->main_image_url,
                'unit_price' => (int) $p->unit_price,
                'sale_price' => $p->sale_price ? (int) $p->sale_price : null,
                'effective_price_formatted' => number_format($effectivePrice, 0, ',', '.') . ' đ/m²',
                'material' => $p->material ?? 'Vải dệt cao cấp',
                'style' => $p->style ?? 'Hiện đại',
            ];
        }
        return $result;
    }

    /**
     * Chuẩn hóa văn bản tiếng Việt
     */
    protected function normalizeText(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
        $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
        $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
        $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
        $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
        $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
        $str = preg_replace('/[đ]/u', 'd', $str);
        return $str;
    }
}
