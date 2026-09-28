import os
import docx
from docx import Document
from docx.shared import Pt, Inches, RGBColor, Cm
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def set_table_borders(table, color="CCCCCC", sz="4", val="single"):
    tblPr = table._tbl.tblPr
    tblBorders = parse_xml(
        f'<w:tblBorders {nsdecls("w")}>'
        f'<w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:left w:val="none"/>'
        f'<w:right w:val="none"/>'
        f'<w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:insideV w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'</w:tblBorders>'
    )
    tblPr.append(tblBorders)

def build_component_report():
    doc = Document()

    # Page Margins: Top 2cm, Bottom 2cm, Left 3cm, Right 2cm
    for section in doc.sections:
        section.top_margin = Cm(2.0)
        section.bottom_margin = Cm(2.0)
        section.left_margin = Cm(3.0)
        section.right_margin = Cm(2.0)

    # Styles
    style_normal = doc.styles['Normal']
    style_normal.font.name = 'Times New Roman'
    style_normal.font.size = Pt(14)
    style_normal.font.color.rgb = RGBColor(0x22, 0x22, 0x22)
    style_normal.paragraph_format.line_spacing = 1.3
    style_normal.paragraph_format.space_after = Pt(6)

    # Helper: Add Header
    def add_title(text):
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run = p.add_run(text)
        run.font.name = 'Times New Roman'
        run.font.size = Pt(18)
        run.bold = True
        run.font.color.rgb = RGBColor(0x1A, 0x22, 0x32) # Dark Navy
        p.paragraph_format.space_after = Pt(4)
        return p

    def add_subtitle(text):
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run = p.add_run(text)
        run.font.name = 'Times New Roman'
        run.font.size = Pt(14)
        run.bold = True
        run.font.color.rgb = RGBColor(0xB8, 0x86, 0x0B) # Dark Gold
        p.paragraph_format.space_after = Pt(14)
        return p

    def add_h1(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Times New Roman'
        run.font.size = Pt(16)
        run.bold = True
        run.font.color.rgb = RGBColor(0x1A, 0x22, 0x32)
        p.paragraph_format.space_before = Pt(12)
        p.paragraph_format.space_after = Pt(6)
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Times New Roman'
        run.font.size = Pt(14.5)
        run.bold = True
        run.font.color.rgb = RGBColor(0x2A, 0x4B, 0x7C)
        p.paragraph_format.space_before = Pt(8)
        p.paragraph_format.space_after = Pt(4)
        return p

    def add_h3(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Times New Roman'
        run.font.size = Pt(14)
        run.bold = True
        run.italic = True
        run.font.color.rgb = RGBColor(0x33, 0x33, 0x33)
        p.paragraph_format.space_before = Pt(6)
        p.paragraph_format.space_after = Pt(3)
        return p

    def add_body(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Times New Roman'
        run.font.size = Pt(14)
        p.paragraph_format.line_spacing = 1.3
        p.paragraph_format.space_after = Pt(6)
        return p

    def add_num_item(num_str, title_str, desc_str):
        p = doc.add_paragraph()
        p.paragraph_format.left_indent = Inches(0.2)
        p.paragraph_format.line_spacing = 1.3
        p.paragraph_format.space_after = Pt(5)
        
        r_num = p.add_run(f"{num_str} ")
        r_num.bold = True
        r_num.font.name = 'Times New Roman'
        r_num.font.size = Pt(14)
        
        r_title = p.add_run(f"{title_str}: ")
        r_title.bold = True
        r_title.font.name = 'Times New Roman'
        r_title.font.size = Pt(14)
        
        r_desc = p.add_run(desc_str)
        r_desc.font.name = 'Times New Roman'
        r_desc.font.size = Pt(14)
        return p

    # HEADER & TITLE
    add_title("BÁO CÁO BÀI TẬP NHÓM: KIẾN TRÚC VÀ THIẾT KẾ PHẦN MỀM")
    add_subtitle("ĐỀ TÀI: THIẾT KẾ THÀNH PHẦN (COMPONENT DESIGN) HỆ THỐNG WEBSITE BÁN VÀ MAY ĐO RÈM CỬA TRỰC TUYẾN (RÈM ONLINE)")

    add_body("Báo cáo này thực hiện đầy đủ 5 nội dung trọng tâm theo yêu cầu của bài tập thiết kế kiến trúc phần mềm, hiện thực hóa trực tiếp vào bài toán xây dựng nền tảng thương mại điện tử và quản lý xưởng may rèm may đo cao cấp (Rèm Online) trên nền tảng Laravel 11.")

    # 1. MỤC 1: XÁC ĐỊNH CÁC THÀNH PHẦN CỐT LÕI
    add_h1("1. XÁC ĐỊNH CÁC THÀNH PHẦN CỐT LÕI VÀ Ý NGHĨA CỦA CÁC THÀNH PHẦN")
    add_body("Hệ thống Rèm Online được phân rã thành các thành phần nghiệp vụ độc lập (High Cohesion, Low Coupling) nhằm đảm bảo tính tái sử dụng, dễ bảo trì và dễ mở rộng. Dưới đây là 10 thành phần cốt lõi của hệ thống:")

    add_num_item("1.1.", "Thành phần Quản lý Người dùng & Xác thực (User/Auth Component)", 
                 "Chịu trách nhiệm quản lý định danh người dùng, xác thực đăng nhập/đăng ký, phân quyền truy cập 4 cấp bậc (Khách hàng - Customer, Nhân viên xưởng/kỹ thuật - Staff, Quản trị viên - Admin, Quản trị tối cao - Super Admin), quản lý hồ sơ cá nhân và sổ địa chỉ giao hàng.")

    add_num_item("1.2.", "Thành phần Quản lý Danh mục & Sản phẩm Rèm (Product/Catalog Component)", 
                 "Chịu trách nhiệm quản lý thông tin các mẫu rèm (rèm vải cao cấp, rèm cầu vồng, rèm cuốn, rèm roman, rèm gỗ...), chất liệu, xuất xứ vải, bảng mẫu màu (color swatches), đơn giá cơ sở theo m² hoặc bộ, và phân loại theo công năng cản sáng.")

    add_num_item("1.3.", "Thành phần Tính toán Giá may đo (PriceCalculator Component)", 
                 "Đây là thành phần nghiệp vụ đặc thù cốt lõi của ngành rèm. Chịu trách nhiệm tính toán diện tích cửa thực tế theo công thức S = Rộng (W) x Cao (H), áp dụng quy chuẩn làm tròn diện tích tối thiểu, nhân đơn giá chất liệu vải và tự động tính phụ phí phụ kiện (thanh treo, động cơ tự động) với độ trễ phản hồi tức thời dưới 10ms.")

    add_num_item("1.4.", "Thành phần Quản lý Giỏ hàng May đo (Cart Component)", 
                 "Chịu trách nhiệm lưu trữ và duy trì trạng thái giỏ hàng cho cả khách vãng lai (Session) và khách hàng đã đăng nhập (Database). Quản lý từng sản phẩm may đo cấu hình riêng (chiều rộng, chiều cao, mã màu, diện tích m² và tổng tiền tương ứng), hỗ trợ chọn lọc từng mục để thanh toán.")

    add_num_item("1.5.", "Thành phần Quản lý Khuyến mãi & Mã giảm giá (Voucher Component)", 
                 "Chịu trách nhiệm quản lý các chương trình ưu đãi, kiểm tra tính hợp lệ của mã khuyến mãi (thời hạn, số lượt sử dụng tối đa, giá trị đơn hàng tối thiểu, danh mục rèm được áp dụng) và tính toán số tiền chiết khấu giảm trực tiếp vào đơn hàng.")

    add_num_item("1.6.", "Thành phần Tích hợp Vận chuyển (Shipping/GHN Component)", 
                 "Chịu trách nhiệm đồng bộ địa giới hành chính 3 cấp (Tỉnh/Thành phố, Quận/Huyện, Phường/Xã) và kết nối API đơn vị vận chuyển Giao Hàng Nhanh (GHN) để tính toán cước phí vận chuyển chính xác theo trọng lượng kiện rèm và khoảng cách giao hàng.")

    add_num_item("1.7.", "Thành phần Xử lý Đơn hàng & Quản lý Xưởng May (Order/Workshop Component)", 
                 "Chịu trách nhiệm quản lý toàn bộ vòng đời đơn hàng (Khởi tạo -> Đã xác nhận -> Đang may xưởng -> Đang giao -> Hoàn thành), xử lý thanh toán (COD / Chuyển khoản ngân hàng) và tự động trích xuất Lệnh sản xuất xưởng may chuẩn khổ giấy in A4 cho thợ may.")

    add_num_item("1.8.", "Thành phần Đặt lịch Khảo sát & Đo đạc Tận nhà (Consultation Booking Component)", 
                 "Chịu trách nhiệm tiếp nhận yêu cầu từ khách hàng có nhu cầu tư vấn xem mẫu vải thực tế và đo đạc khung cửa tại nhà/công trình, quản lý lịch hẹn và điều phối nhân viên kỹ thuật phụ trách khảo sát.")

    add_num_item("1.9.", "Thành phần Đánh giá & Phản hồi Khách hàng (Review/Rating Component)", 
                 "Chịu trách nhiệm quản lý việc khách hàng đánh giá chất lượng sản phẩm (thang điểm 1 đến 5 sao kèm hình ảnh thực tế) sau khi nhận rèm, đồng thời hỗ trợ ban quản trị kiểm duyệt và phản hồi đánh giá.")

    add_num_item("1.10.", "Thành phần Báo cáo Thống kê Doanh thu (Analytics Component)", 
                 "Chịu trách nhiệm tổng hợp số liệu kinh doanh, biểu đồ doanh thu theo chu kỳ thời gian (12 tháng/theo ngày), thống kê sản lượng m² rèm may đo đã hoàn thành, đo lường hiệu quả sử dụng voucher và xuất dữ liệu báo cáo dạng CSV/Excel.")

    # 2. MỤC 2: XÁC ĐỊNH MỐI QUAN HỆ DEPENDENCY
    add_h1("2. XÁC ĐỊNH CÁC MỐI QUAN HỆ (DEPENDENCY) VÀ Ý NGHĨA CỦA CÁC MỐI QUAN HỆ")
    add_body("Trong kiến trúc hệ thống Rèm Online, các thành phần tương tác chặt chẽ với nhau thông qua các quan hệ phụ thuộc (Dependencies) rõ ràng để hoàn thành các luồng nghiệp vụ phức tạp:")

    add_num_item("2.1.", "Quan hệ giữa PriceCalculator và Product (PriceCalculator -> Product)", 
                 "Ý nghĩa: Để tính toán chính xác giá may đo một bộ rèm theo kích thước tùy chỉnh, PriceCalculator bắt buộc phải truy vấn thông tin đơn giá cơ sở (price/m²), quy chuẩn diện tích tối thiểu và bảng giá phụ kiện từ thành phần Product.")

    add_num_item("2.2.", "Quan hệ giữa Cart và Product, PriceCalculator, User (Cart -> Product, PriceCalculator, User)", 
                 "Ý nghĩa: Khi người dùng thêm một bộ rèm vào giỏ hàng, Cart cần lấy thông tin mô tả và mẫu màu từ Product, gọi dịch vụ PriceCalculator để tính thành tiền theo kích thước W x H, và liên kết mục giỏ hàng này với tài khoản khách hàng thông qua User Component.")

    add_num_item("2.3.", "Quan hệ giữa Order và Cart, User, Voucher, Shipping (Order -> Cart, User, Voucher, Shipping)", 
                 "Ý nghĩa: Quá trình thanh toán và lập đơn hàng (Checkout) là trung tâm điều phối: Order lấy dữ liệu sản phẩm may đo từ Cart, thông tin liên hệ và sổ địa chỉ từ User, gọi Voucher để khấu trừ tiền giảm giá, và gọi Shipping để cộng cước phí vận chuyển chính xác.")

    add_num_item("2.4.", "Quan hệ giữa Order và Workshop (Order -> Workshop/Database)", 
                 "Ý nghĩa: Sau khi đơn hàng được đặt và xác nhận, thông tin kỹ thuật may đo chi tiết (chiều rộng, chiều cao, diện tích m², mã màu vải) từ Order được chuyển tiếp đến phân hệ Workshop để in Phiếu lệnh may xưởng khổ A4 cho thợ may sản xuất.")

    add_num_item("2.5.", "Quan hệ giữa Voucher và Cart, Order (Voucher -> Cart, Order)", 
                 "Ý nghĩa: Thành phần Voucher cần đọc giá trị tạm tính (Subtotal) và danh mục các mặt hàng trong Cart/Order để kiểm tra xem đơn hàng có thỏa mãn các quy tắc giảm giá (ví dụ: đơn hàng trên 2.000.000đ hoặc chỉ áp dụng cho rèm cầu vồng) hay không.")

    add_num_item("2.6.", "Quan hệ giữa Shipping và Database, GHN External API (Shipping -> Database, GHN API)", 
                 "Ý nghĩa: Shipping Component phụ thuộc vào cơ sở dữ liệu mã đơn vị hành chính 3 cấp và dịch vụ API ngoài của Giao Hàng Nhanh (GHN) để tính cước phí và dự kiến thời gian giao hàng.")

    add_num_item("2.7.", "Quan hệ giữa Consultation Booking và User (Consultation Booking -> User)", 
                 "Ý nghĩa: Đăng ký đặt lịch khảo sát tại nhà cần liên kết với thông tin khách hàng (User) để lưu lịch sử yêu cầu và phân bổ nhân viên kỹ thuật (Staff) liên hệ chăm sóc.")

    add_num_item("2.8.", "Quan hệ giữa Review và Order, Product, User (Review -> Order, Product, User)", 
                 "Ý nghĩa: Đảm bảo tính minh bạch và trung thực (Verified Purchase), hệ thống chỉ cho phép tạo đánh giá khi có sự xác nhận rằng người dùng (User) đã hoàn thành đơn hàng (Order) có chứa sản phẩm rèm đó (Product).")

    add_num_item("2.9.", "Quan hệ giữa Analytics và Order, Product, Voucher (Analytics -> Order, Product, Voucher)", 
                 "Ý nghĩa: Báo cáo phân tích doanh thu và sản lượng phụ thuộc hoàn toàn vào dữ liệu lịch sử đơn hàng (Order), danh mục sản phẩm bán ra (Product) và chi phí giảm giá (Voucher).")

    # 3. MỤC 3: XÁC ĐỊNH CÁC INTERFACE
    add_h1("3. XÁC ĐỊNH CÁC INTERFACE (HÀM CUNG CẤP CỦA TỪNG THÀNH PHẦN)")
    add_body("Mỗi thành phần công khai một tập hợp các giao diện (Interface) chuẩn hóa nhằm phục vụ việc giao tiếp giữa các tầng và giữa các component:")

    add_num_item("3.1.", "Giao diện Quản lý Người dùng (IUserService / IAuthService)", 
                 "login(credentials): AuthToken | register(userData): User | getProfile(userId): UserProfile | updateAddress(userId, addressData): bool | getStaffList(): List<User>")

    add_num_item("3.2.", "Giao diện Sản phẩm Rèm (IProductService / ICatalogService)", 
                 "getProductList(filters): List<Product> | getProductDetail(productId): Product | getProductColors(productId): List<ColorSwatch> | searchProducts(keyword): List<Product> | getCategories(): List<Category>")

    add_num_item("3.3.", "Giao diện Tính giá May đo (IPriceCalculatorService)", 
                 "calculateItemPrice(productId, width, height, colorId): PriceResult | calculateArea(width, height): float | validateDimensions(width, height): DimensionValidationResult")

    add_num_item("3.4.", "Giao diện Giỏ hàng (ICartService)", 
                 "addToCart(userId, productId, width, height, colorId, quantity): CartItem | getCart(userId/sessionId): Cart | updateQuantity(cartItemId, quantity): bool | removeFromCart(cartItemId): bool | clearCart(userId): void")

    add_num_item("3.5.", "Giao diện Khuyến mãi (IVoucherService)", 
                 "validateVoucher(code, orderSubtotal, userId): VoucherValidationResult | applyVoucher(code, subtotal): DiscountAmount | markVoucherUsed(code, orderId): bool | getActiveVouchers(): List<Voucher>")

    add_num_item("3.6.", "Giao diện Vận chuyển (IShippingService / IGHNService)", 
                 "getProvinces(): List<Province> | getDistricts(provinceId): List<District> | getWards(districtId): List<Ward> | calculateShippingFee(toDistrictId, toWardCode, weight): ShippingFeeResult")

    add_num_item("3.7.", "Giao diện Đơn hàng & Xưởng may (IOrderService / IWorkshopService)", 
                 "createOrder(orderDto): Order | getOrderDetail(orderId): OrderDetail | updateOrderStatus(orderId, status): bool | getOrdersByUser(userId): List<Order> | generateWorkshopPrintDoc(orderId): WorkOrderDoc")

    add_num_item("3.8.", "Giao diện Đặt lịch Khảo sát (IConsultationService)", 
                 "bookConsultation(consultationDto): Consultation | getConsultationList(filters): List<Consultation> | assignTechnician(consultationId, staffId): bool | updateConsultationStatus(id, status): bool")

    add_num_item("3.9.", "Giao diện Đánh giá (IReviewService)", 
                 "submitReview(userId, orderId, productId, rating, comment, images): Review | getProductReviews(productId): List<Review> | moderateReview(reviewId, status): bool | canUserReview(userId, productId): bool")

    add_num_item("3.10.", "Giao diện Thống kê Báo cáo (IAnalyticsService)", 
                 "getRevenueStatistics(timeRange): RevenueStats | getTopSellingProducts(limit): List<ProductSales> | getCategoryShare(): List<CategoryShare> | exportAnalyticsToCsv(type, dateRange): FileStream")

    # 4. MỤC 4: TẠO BẢNG TỔNG HỢP THEO MẪU
    add_h1("4. BẢNG TỔNG HỢP CÁC THÀNH PHẦN THEO MẪU QUY ĐỊNH")
    add_body("Bảng tổng hợp dưới đây được chuẩn hóa theo mẫu của môn học, thể hiện tường minh Tên thành phần, Trách nhiệm, Interface cung cấp, Phụ thuộc (Dependency) và Giải thích chi tiết liên kết nghiệp vụ:")

    table_data = [
        ("Component", "Trách nhiệm", "Interface (Hàm cung cấp)", "Dependency (Cần dùng tới)", "Giải thích liên kết"),
        ("User (Auth)", "Quản lý người dùng, phân quyền 4 cấp, hồ sơ và sổ địa chỉ", "login(), register(), getProfile(), updateAddress()", "Database", "Xác thực và lưu trữ thông tin tài khoản trực tiếp vào cơ sở dữ liệu."),
        ("Product", "Quản lý mẫu rèm, chất liệu, màu sắc và đơn giá cơ sở", "getProductList(), getProductDetail(), getColors()", "Database", "Tìm kiếm, lọc danh mục và truy xuất dữ liệu sản phẩm từ cơ sở dữ liệu."),
        ("PriceCalculator", "Tính diện tích m² và tính giá may đo rèm tự động", "calculateItemPrice(), calculateArea(), validateDimensions()", "Product", "Để tính giá may đo cần lấy đơn giá cơ sở, phụ phí và hệ số chất liệu từ thành phần Product."),
        ("Cart", "Quản lý giỏ hàng cấu hình kích thước và màu rèm", "addToCart(), getCart(), updateQuantity(), removeFromCart()", "Product, PriceCalculator, User", "Mỗi mục rèm trong giỏ hàng cần biết thông tin sản phẩm (Product), giá tính theo m² (PriceCalculator) và gán với người dùng (User)."),
        ("Voucher", "Quản lý và tính chiết khấu mã giảm giá", "validateVoucher(), applyVoucher(), markVoucherUsed()", "Cart, Order, Database", "Kiểm tra điều kiện áp dụng dựa trên tổng tiền tạm tính của Cart/Order và ghi nhận lượt dùng vào Database."),
        ("Shipping", "Đồng bộ địa chỉ 3 cấp và tính cước vận chuyển", "getProvinces(), getDistricts(), calculateShippingFee()", "GHN API, Database", "Kết nối dịch vụ API Giao Hàng Nhanh và cơ sở dữ liệu hành chính để tính toán cước phí chính xác."),
        ("Order", "Xử lý đơn hàng, xuất lệnh sản xuất xưởng may khổ A4", "createOrder(), getOrderDetail(), updateOrderStatus(), generateWorkshopPrintDoc()", "Cart, User, Voucher, Shipping, Database", "Quá trình thanh toán cần gom sản phẩm từ Cart, thông tin nhận hàng từ User, tiền giảm từ Voucher, phí giao từ Shipping để tạo đơn và xuất lệnh may."),
        ("Consultation", "Đặt lịch hẹn khảo sát, đo đạc mẫu vải tại nhà", "bookConsultation(), getConsultationList(), assignTechnician()", "User, Database", "Tiếp nhận lịch hẹn của khách hàng (User), ghi nhận địa chỉ khảo sát và phân công kỹ thuật viên xử lý."),
        ("Review", "Đánh giá chất lượng sản phẩm (1-5 sao) và hình ảnh", "submitReview(), getProductReviews(), moderateReview()", "Order, Product, User, Database", "Chỉ người dùng (User) đã hoàn thành đơn hàng (Order) chứa sản phẩm đó (Product) mới được phép gửi đánh giá."),
        ("Analytics", "Thống kê doanh thu, sản lượng m² rèm và xuất file", "getRevenueStatistics(), getTopSellingProducts(), exportAnalyticsToCsv()", "Order, Product, Voucher, Database", "Tổng hợp số liệu kinh doanh từ các đơn hàng thành công (Order), danh mục sản phẩm (Product) và các chiến dịch voucher.")
    ]

    table = doc.add_table(rows=len(table_data), cols=5)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table, color="B8860B", sz="6", val="single")

    col_widths = [Cm(2.2), Cm(3.2), Cm(3.8), Cm(2.8), Cm(4.0)]

    for row_idx, row_data in enumerate(table_data):
        row = table.rows[row_idx]
        for col_idx, cell_value in enumerate(row_data):
            cell = row.cells[col_idx]
            cell.width = col_widths[col_idx]
            set_cell_margins(cell, top=120, bottom=120, left=140, right=140)
            
            p = cell.paragraphs[0]
            p.paragraph_format.line_spacing = 1.15
            p.paragraph_format.space_after = Pt(2)
            
            run = p.add_run(cell_value)
            run.font.name = 'Times New Roman'
            
            if row_idx == 0:
                set_cell_background(cell, "1A2232") # Dark Navy
                run.bold = True
                run.font.size = Pt(11)
                run.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF) # White
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            else:
                run.font.size = Pt(10.5)
                run.font.color.rgb = RGBColor(0x22, 0x22, 0x22)
                if row_idx % 2 == 1:
                    set_cell_background(cell, "F9F9F6") # Light tint
                else:
                    set_cell_background(cell, "FFFFFF")
                if col_idx == 0:
                    run.bold = True
                    p.alignment = WD_ALIGN_PARAGRAPH.LEFT
                elif col_idx in [1, 4]:
                    p.alignment = WD_ALIGN_PARAGRAPH.LEFT
                else:
                    p.alignment = WD_ALIGN_PARAGRAPH.LEFT

    doc.add_paragraph().paragraph_format.space_after = Pt(10)

    # 5. MỤC 5: VẼ SƠ ĐỒ THÀNH PHẦN (COMPONENT DIAGRAM)
    add_h1("5. SƠ ĐỒ THÀNH PHẦN KIẾN TRÚC HỆ THỐNG (COMPONENT DIAGRAM)")
    add_body("Sơ đồ thành phần dưới đây mô tả cấu trúc kiến trúc tổng thể của hệ thống Rèm Online theo chuẩn UML 2.0, phân chia rõ rệt thành 3 phân tầng chính: Tầng Giao diện người dùng (Presentation Layer), Tầng Xử lý Nghiệp vụ & Dịch vụ (Core Business Services Layer) và Tầng Truy cập Dữ liệu & Dịch vụ Ngoài (Data & External Layer):")

    add_h2("5.1. Bố cục phân tầng kiến trúc thành phần (Architectural Layering)")
    add_num_item("a)", "Tầng Trình diễn (Presentation Components)", 
                 "Bao gồm Web Client (Khách hàng mua sắm, tính giá m² trực tiếp, đặt lịch khảo sát) và Admin Dashboard (Bảng điều khiển quản trị, phân tích doanh thu, in phiếu lệnh xưởng may A4).")
    
    add_num_item("b)", "Tầng Thành phần Nghiệp vụ Lõi (Business Service Components)", 
                 "Gồm 10 thành phần độc lập: User/Auth, Product Catalog, Price Calculator, Cart, Voucher/Promotion, Shipping/GHN, Order & Workshop, Consultation Booking, Review & Rating, Analytics.")

    add_num_item("c)", "Tầng Dữ liệu & Dịch vụ Ngoại vi (Data Access & External Services)", 
                 "Gồm Cơ sở dữ liệu quan hệ MySQL (Database Component), Dịch vụ API ngoài Giao Hàng Nhanh (GHN External API) và Cổng thanh toán trực tuyến.")

    add_h2("5.2. Biểu diễn Sơ đồ Thành phần UML (Component Diagram Specification)")
    add_body("Sơ đồ luồng quan hệ phụ thuộc (Dependency Diagram) giữa các thành phần được chuẩn hóa như sau:")

    # Box for textual UML Diagram
    diag_box = [
        "+---------------------------------------------------------------------------------------------------+",
        "|                                     PRESENTATION LAYER (UI)                                       |",
        "|  [Customer Web Interface]      [Staff Consultation Portal]       [Admin Dashboard & Workshop A4]  |",
        "+--------------------------------------------------+------------------------------------------------+",
        "                                                   | (HTTP Requests / REST / Blade)",
        "                                                   v",
        "+---------------------------------------------------------------------------------------------------+",
        "|                                     CORE BUSINESS COMPONENTS                                      |",
        "|                                                                                                   |",
        "|   +-------------------+        +-------------------+        +---------------------------------+   |",
        "|   |   User Component  |        | Product Component |<-------|     PriceCalculator Component   |   |",
        "|   | (Auth / Profile)  |        | (Catalog/Swatches)|        | (S = W x H, Realtime Calculator)|   |",
        "|   +---------+---------+        +---------+---------+        +----------------+----------------+   |",
        "|             |                            ^                                   ^                    |",
        "|             |                            |                                   |                    |",
        "|             +---------------+------------|-----------------------------------+                    |",
        "|                             |            |                                                        |",
        "|                             v            |                                                        |",
        "|                        +----+------------+----+                                                   |",
        "|                        |    Cart Component    |<-------------------+                              |",
        "|                        | (Custom Size & Price)|                    |                              |",
        "|                        +----+------------+----+                    |                              |",
        "|                             |            |                         |                              |",
        "|             +---------------+            +------------+            |                              |",
        "|             |                                         |            |                              |",
        "|             v                                         v            |                              |",
        "|   +---------+---------+        +-------------------+  |   +--------+--------+                     |",
        "|   | Consultation Comp |        | Voucher Component |  |   | Order & Workshop|                     |",
        "|   | (Home Measurement)|        | (Discount Rules)  |  |   | (A4 Work Order) |                     |",
        "|   +-------------------+        +---------+---------+  |   +--------+--------+                     |",
        "|                                          |            |            |                              |",
        "|                                          +------------|------------+                              |",
        "|                                                       |            |                              |",
        "|             +-----------------------------------------+            v                              |",
        "|             v                                                +-----+-----------+                  |",
        "|   +---------+---------+                                      | Shipping (GHN)  |                  |",
        "|   | Review Component  |                                      | (3-tier Address)|                  |",
        "|   | (Verified Ratings)|                                      +-----+-----------+                  |",
        "|   +-------------------+                                            |                              |",
        "|             |                                                      |                              |",
        "|             +----------------------------+-------------------------+                              |",
        "|                                          v                                                        |",
        "|                                +---------+---------+                                              |",
        "|                                | Analytics Comp    |                                              |",
        "|                                | (Revenue & Export)|                                              |",
        "|                                +---------+---------+                                              |",
        "+------------------------------------------|--------------------------------------------------------+",
        "                                           | (Eloquent ORM / API Client)",
        "                                           v",
        "+---------------------------------------------------------------------------------------------------+",
        "|                                  DATA & EXTERNAL SERVICE LAYER                                    |",
        "|            [( MySQL Database )]                     [( Giao Hàng Nhanh API )]                     |",
        "+---------------------------------------------------------------------------------------------------+"
    ]

    p_code = doc.add_paragraph()
    p_code.paragraph_format.left_indent = Inches(0.1)
    p_code.paragraph_format.line_spacing = 1.0
    p_code.paragraph_format.space_after = Pt(8)
    for line in diag_box:
        run = p_code.add_run(line + "\n")
        run.font.name = 'Courier New'
        run.font.size = Pt(8.5)
        run.font.color.rgb = RGBColor(0x1A, 0x22, 0x32)

    add_h2("5.3. Đánh giá chất lượng thiết kế thành phần")
    add_num_item("1.", "Tính kết dính nội tại cao (High Cohesion)", 
                 "Mỗi thành phần chỉ tập trung đảm nhiệm duy nhất một chức năng nghiệp vụ cụ thể. Ví dụ: PriceCalculator chỉ đảm nhận giải thuật hình học và tính tiền may đo, không phụ thuộc vào trạng thái đăng nhập hay giao diện web.")

    add_num_item("2.", "Tính ghép nối lỏng lẻo (Low Coupling)", 
                 "Các thành phần giao tiếp với nhau hoàn toàn thông qua Service Interface công khai. Khi thay đổi thuật ngữ chiết khấu trong Voucher hoặc thay đổi thuật toán tính phí giao hàng GHN, cấu trúc của Order và Cart hoàn toàn không bị ảnh hưởng.")

    add_num_item("3.", "Khả năng mở rộng và kiểm thử (Scalability & Testability)", 
                 "Thiết kế phân tầng Interface rõ ràng cho phép viết Unit Test độc lập (Mock Object) cho từng thành phần (như kiểm thử logic tính diện tích m² với các biên độ kích thước khác nhau) và sẵn sàng nâng cấp thành kiến trúc Microservices trong tương lai.")

    # Save Document
    target_path = "/Users/dongquangminh/.gemini/antigravity/scratch/rem-online/Bao_Cao_Thiet_Ke_Thanh_Phan_Rem_Online.docx"
    doc.save(target_path)
    print("SUCCESS: File saved at", target_path)

if __name__ == "__main__":
    build_component_report()
