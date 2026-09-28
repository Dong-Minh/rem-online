# HƯỚNG DẪN TRIỂN KHAI (DEPLOYMENT) DỰ ÁN RÈM ONLINE LÊN RENDER & AIVEN MYSQL

Tài liệu này tổng hợp toàn bộ quy trình đưa ứng dụng Laravel Rèm Cửa Online từ máy cá nhân lên môi trường Internet, sử dụng nền tảng **Render (Web Service với Docker)** và **Aiven (Managed Cloud MySQL)**.

---

## 1. MÔ HÌNH KIẾN TRÚC VÀ CÁC THÀNH PHẦN CỐT LÕI
1. **Mã nguồn trên GitHub:** Kho lưu trữ trung gian. Khi lập trình viên push code, Render tự động nhận webhook để kích hoạt quá trình build & deploy (CI/CD).
2. **Container Docker trên Render:**
   - **Tini (`/sbin/tini`):** Tiến trình quản lý PID 1, thu hồi zombie process và chuyển tiếp tín hiệu tắt/khởi động an toàn.
   - **Entrypoint (`docker/entrypoint.sh`):** Điểm điều phối khởi động: phân quyền chứng chỉ CA SSL, kiểm tra biến môi trường, tối ưu cache (`config:cache`, `route:cache`, `view:cache`), tự động chạy `migrate` và `db:seed`.
   - **Nginx FastCGI Server:** Tiếp nhận yêu cầu HTTP từ Internet tại cổng động `$PORT`, phục vụ file tĩnh và chuyển tiếp các request động qua FastCGI cổng `127.0.0.1:9000` tới PHP-FPM.
   - **PHP-FPM 8.2/8.4:** Xử lý toàn bộ logic mã nguồn Laravel.
3. **Cơ sở dữ liệu Aiven Cloud MySQL:**
   - Dịch vụ MySQL Cloud độc lập, bảo mật bằng giao thức mã hóa SSL/TLS với chứng chỉ CA (`ca.pem`).

---

## 2. TRÌNH TỰ CÁC BƯỚC THỰC HIỆN THỰC TẾ

### BƯỚC 1: TẠO VÀ CẤU HÌNH CƠ SỞ DỮ LIỆU TRÊN AIVEN.IO
1. Truy cập [https://aiven.io](https://aiven.io) và đăng nhập.
2. Nhấn **Create Service** -> Chọn **MySQL**.
3. Chọn Cloud Provider: **AWS hoặc Google Cloud** -> Chọn Region gần Việt Nam nhất (**Singapore - ap-southeast-1**).
4. Chọn gói **Free Plan** -> Đặt tên Service (VD: `rem-online-db`) -> Nhấn **Create Service**.
5. Chờ trạng thái chuyển sang **RUNNING**:
   - Ghi lại các thông số: **Host**, **Port**, **User** (`avnadmin`), **Password**, **Database** (`defaultdb`).
   - Tải file chứng chỉ **CA Certificate** (file `ca.pem`) về máy tính.

---

### BƯỚC 2: ĐẨY TOÀN BỘ MÃ NGUỒN LÊN GITHUB
Tại thư mục dự án trên máy tính, mở Terminal và chạy:
```bash
git status
git add .
git commit -m "Configure Dockerfile, Nginx, PHP-FPM and Seeder for Render deployment"
git push origin main
```

---

### BƯỚC 3: TẠO WEB SERVICE TRÊN RENDER.COM
1. Truy cập [https://render.com](https://render.com) và đăng nhập.
2. Nhấn **New +** -> Chọn **Web Service**.
3. Chọn kho mã nguồn GitHub của bạn (`rem-online`).
4. Cấu hình cơ bản:
   - **Name:** `rem-online` (hoặc tên tùy chọn)
   - **Region:** `Singapore (Southeast Asia)`
   - **Branch:** `main`
   - **Root Directory:** Để trống (nếu dự án ở thư mục gốc)
   - **Runtime:** Chọn **Docker** (Render sẽ tự động đọc `Dockerfile` ở thư mục gốc)
   - **Instance Type:** Chọn gói **Free**

---

### BƯỚC 4: THÊM SECRET FILE (CHỨNG CHỈ SSL CA)
1. Trong phần cấu hình Service trên Render, cuộn xuống mục **Advanced** -> Chọn **Secret Files**.
2. Nhấn **Add Secret File**:
   - **Filename:** `ca.pem`
   - **File Contents:** Mở file `ca.pem` đã tải từ Aiven bằng Notepad/TextEdit, copy toàn bộ nội dung (bao gồm cả dòng `-----BEGIN CERTIFICATE-----` và `-----END CERTIFICATE-----`) và dán vào ô này.
3. Nhấn **Save**.

---

### BƯỚC 5: KHAI BÁO BIẾN MÔI TRƯỜNG (ENVIRONMENT VARIABLES)
Mở tab **Environment** trên Render và thêm các biến môi trường sau:

| Tên biến (Key) | Giá trị mẫu (Value) | Giải thích |
|---|---|---|
| `APP_NAME` | `Rem Online` | Tên website |
| `APP_ENV` | `production` | Môi trường production |
| `APP_DEBUG` | `false` | Tắt chế độ debug để bảo mật |
| `APP_URL` | `https://rem-online.onrender.com` | Địa chỉ HTTPS do Render cấp |
| `APP_KEY` | *(Lấy từ file .env local của bạn)* | Khóa mã hóa bảo mật Laravel |
| `TRUSTED_PROXIES` | `*` | Nhận diện đúng HTTPS qua Proxy Render |
| `DB_CONNECTION` | `mysql` | Loại kết nối database |
| `DB_HOST` | `mysql-xxxxx.aivencloud.com` | Host MySQL lấy từ Aiven |
| `DB_PORT` | `12345` | Cổng MySQL lấy từ Aiven |
| `DB_DATABASE` | `defaultdb` | Tên cơ sở dữ liệu trên Aiven |
| `DB_USERNAME` | `avnadmin` | Tài khoản quản trị database Aiven |
| `DB_PASSWORD` | `xxxxxxxxx` | Mật khẩu database Aiven |
| `MYSQL_ATTR_SSL_CA` | `/etc/secrets/ca.pem` | Đường dẫn chứng chỉ CA trên Render |
| `SESSION_DRIVER` | `database` | Lưu session vào database |
| `SESSION_SECURE_COOKIE` | `true` | Chỉ gửi cookie qua kết nối bảo mật HTTPS |
| `CACHE_STORE` | `database` | Lưu cache ứng dụng |
| `QUEUE_CONNECTION` | `sync` | Hàng đợi đồng bộ |
| `LOG_CHANNEL` | `stderr` | Đẩy log ra console để Render hiển thị |
| `PORT` | `10000` | Cổng Nginx lắng nghe |
| `RUN_MIGRATIONS` | `true` | Tự động tạo bảng khi khởi động |
| `RUN_SEEDERS` | `true` *(Đổi về `false` sau lần đầu)* | Tự động nạp 35 sản phẩm rèm & tài khoản admin |
| `SEED_ADMIN_NAME` | `Quản Trị Viên` | Tên admin khởi tạo |
| `SEED_ADMIN_EMAIL` | `admin@remonline.vn` | Email đăng nhập admin |
| `SEED_ADMIN_PASSWORD` | `MatKhauBaoMat123456!` | Mật khẩu admin ($\ge 12$ ký tự) |

---

### BƯỚC 6: TRIỂN KHAI VÀ THEO DÕI LOGS
1. Nhấn **Create Web Service** (hoặc **Manual Deploy** -> **Deploy latest commit**).
2. Theo dõi tab **Logs**:
   - Giai đoạn 1: Build Docker image và cài đặt thư viện PHP bằng Composer.
   - Giai đoạn 2: Container khởi động, script `entrypoint.sh` xác thực chứng chỉ `ca.pem`, thực hiện `artisan migrate` và `artisan db:seed`.
   - Giai đoạn 3: Nginx và PHP-FPM khởi động thành công, Health Check `/up` trả về HTTP 200.
3. Mở đường link `.onrender.com` để truy cập website, đăng nhập quản trị và trải nghiệm!

---

## 3. BỘ CÂU HỎI PHẢN BIỆN KHI THẦY GIÁO HỎI (VÀ CÁCH TRẢ LỜI ĐẠT ĐIỂM TỐI ĐA)

**Câu 1: Tại sao phải sử dụng Dockerfile Multi-stage build mà không dùng 1 stage thông thường?**
- *Trả lời:* Multi-stage build tách biệt giai đoạn cài đặt (Composer, công cụ build phụ trợ) và giai đoạn chạy Production. Điều này giúp loại bỏ toàn bộ các công cụ phát triển không cần thiết, giảm kích thước Image từ ~800MB xuống chỉ còn ~120MB, tăng tốc độ triển khai và hạn chế tối đa các lỗ hổng bảo mật.

**Câu 2: Cơ chế hoạt động của `entrypoint.sh` là gì và tại sao cần Tini?**
- *Trả lời:* `entrypoint.sh` điều phối việc cấu hình trước khi chạy: kiểm tra chứng chỉ SSL `ca.pem`, thay thế cổng `$PORT` động vào cấu hình Nginx, đóng băng cache Laravel và tự động chạy migration/seed. `tini` đóng vai trò quản lý tiến trình PID 1, giúp chuyển tiếp tín hiệu tắt (SIGTERM) chuẩn xác cho cả Nginx và PHP-FPM, tránh hiện tượng tiến trình ma (zombie process).

**Câu 3: Làm thế nào để kết nối MySQL trên Aiven một cách an toàn?**
- *Trả lời:* Aiven bắt buộc giao tiếp mã hóa qua SSL/TLS. Chúng em sử dụng cơ chế Secret File của Render để đưa chứng chỉ `ca.pem` vào container tại `/etc/secrets/ca.pem`. Script `entrypoint.sh` sẽ phân quyền chỉ cho user `www-data` đọc và cấu hình biến `MYSQL_ATTR_SSL_CA` trong `config/database.php`.

**Câu 4: Quản lý bảo mật tài khoản Quản trị viên (Admin) trên môi trường Cloud như thế nào?**
- *Trả lời:* Không lưu cứng (hard-code) mật khẩu admin trong mã nguồn. Toàn bộ thông tin khởi tạo admin được nạp thông qua biến môi trường an toàn của Render (`SEED_ADMIN_EMAIL`, `SEED_ADMIN_PASSWORD`). Seeder `AdminUserSeeder` kiểm tra mật khẩu tối thiểu 12 ký tự, mã hóa qua `Hash::make()` (Bcrypt/Argon2) và bảo lưu tài khoản nếu đã tồn tại chứ không ghi đè.
