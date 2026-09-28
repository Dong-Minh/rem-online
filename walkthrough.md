# Walkthrough — Tiến Độ Dự Án RÈM ONLINE

---

## ✅ PHASE 1: Authentication & Phân Quyền (Hoàn thành)
- Đăng ký (Họ tên, Email, Số điện thoại, Mật khẩu).
- Đăng nhập (Ghi nhớ, Quên mật khẩu, Nút click tài khoản demo).
- Xác thực Email `MustVerifyEmail`.
- Middleware `CheckRole` (`customer`, `staff`, `admin`, `super_admin`).
- Toàn bộ giao diện Bootstrap 5 tiếng Việt sang trọng.

---

## ✅ PHASE 2: Danh Mục & Sản Phẩm Core (Hoàn thành)
- Models & Relationships: `Category`, `Product`, `ProductImage`, `Color`.
- Admin CRUD Quản lý Danh mục & Sản phẩm rèm.
- Khách hàng: Trang chủ & Trang Cửa hàng với bộ lọc Sidebar đa tiêu chí.

---

## ✅ PHASE 3: Chi Tiết Sản Phẩm & Tính Giá Theo m² Realtime (Hoàn thành)
- Service `PriceCalculator.php` chuẩn hóa công thức may đo rèm.
- Trang chi tiết sản phẩm rèm (`/products/{slug}`) với Gallery ảnh, Bảng chọn màu, Nhập kích thước (Rộng $\times$ Cao) và Hộp tính giá tự động Realtime.
- Bảng thông số kỹ thuật & Hướng dẫn tự đo cửa sổ tại nhà (Đo lọt lòng & Đo phủ bì).

---

## ✅ PHASE 4: Giỏ Hàng (Cart) & Checkbox Chọn Món (Hoàn thành)
- Service `CartService.php` quản lý giỏ hàng 2 tầng (Session cho khách, Database cho user).
- Checkbox trước từng bộ rèm cho phép khách hàng chọn món muốn thanh toán ngay và giữ lại các món khác cho lần mua sau.
- Tự động gộp giỏ hàng (Merge Cart) khi đăng nhập.

---

## ✅ MODULE GHN & THANH TOÁN CHECKOUT (Hoàn thành chuẩn hóa theo LAB)

### 1. Cấu hình & Kết nối API GHN
* **Token:** `670c14f5-ab38-11f1-a973-aee5264794df`
* **Shop ID:** `217919`
* **Kho gốc gửi hàng:** `322/76 Ngách 76 Ngõ 322 Mỹ Đình, P. Mỹ Đình 1, Q. Nam Từ Liêm, Hà Nội` (District ID: `1450`, Ward Code: `1A0707`).
* Cấu hình trong `.env` & `config/services.php`.

### 2. Services & Controllers
* **`GHNService.php`:** Gọi API GHN lấy danh mục Tỉnh/Thành $\rightarrow$ Quận/Huyện $\rightarrow$ Phường/Xã, tính cước vận chuyển giao hàng và hủy mã vận đơn.
* **`GHNOrderService.php`:** Đóng gói đơn hàng rèm cửa (quy đổi kích thước $m^2$ ra trọng lượng gram), gọi API tạo vận đơn GHN và cập nhật `ghn_order_code`.
* **`GHNController.php`:** Cung cấp API endpoints cho Frontend fetch địa giới hành chính và tính cước.
* **`OrderController.php`:** Quản lý trang thanh toán, tạo đơn hàng snapshot thông số rèm và quản lý lịch sử đơn hàng.

### 3. Giao diện Thanh toán (`/checkout`)
* Form thông tin người nhận hàng (Họ tên, SĐT, Email).
* **Dropdown 3 cấp GHN:** Tỉnh/Thành phố $\rightarrow$ Quận/Huyện $\rightarrow$ Phường/Xã $\rightarrow$ Tự động gọi API GHN tính cước phí chính xác thời gian thực.
* Tự động cộng tiền hàng + Cước GHN ra **Tổng cộng thanh toán**.
* Trang chi tiết đơn hàng (`/orders/{order}`) hiển thị **Mã vận đơn GHN**, thông số rèm đã đặt và nút hủy đơn hàng.
