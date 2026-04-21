# 📋 Hướng Dẫn Sử Dụng Components - Quản lý Dịch vụ & Hóa Đơn

## 🎯 Tổng Quan

Bạn đã nhận được 2 component Vue 3 hiện đại với design SaaS Minimalist:
1. **Quản lý Dịch vụ** (Service Management)
2. **Xuất/In Hóa Đơn** (Bill Printer)

---

## 1️⃣ Component: Quản lý Dịch vụ

**Vị trí file:** `src/components/admin/dichvu/index.vue`

### ✨ Tính năng chính:
- ✅ **Hiển thị danh sách**: Grid cards đẹp với hình ảnh placeholder
- ✅ **Tìm kiếm thực thời**: Search by tên hoặc loại dịch vụ
- ✅ **CRUD đầy đủ**: Thêm, chỉnh sửa, xóa dịch vụ
- ✅ **Modal hiện đại**: Form validation rõ ràng
- ✅ **Thông báo toast**: Success/Error notifications
- ✅ **Loading states**: UX tốt khi chờ dữ liệu
- ✅ **Responsive design**: Hoạt động trên mobile/tablet/desktop

### 📦 API Endpoints:
```
GET    /admin/dich-vu/get-data           → Lấy danh sách
POST   /admin/dich-vu/create-data        → Thêm mới
POST   /admin/dich-vu/update-data        → Cập nhật
POST   /admin/dich-vu/delete-data        → Xóa
```

### 🎨 Styling Highlights:
- **Màu sắc**: Gradient xanh dương hiện đại (#3b82f6)
- **Border Radius**: 12px-16px (bo góc mềm mại)
- **Box Shadow**: Đổ bóng nhẹ, không cOperation nặng
- **Animation**: Smooth transitions, hover effects

### 🔧 Cách dùng:
```vue
<!-- Thêm vào router hoặc component cha -->
<template>
  <DichVuManagement />
</template>

<script setup>
import DichVuManagement from '@/components/admin/dichvu/index.vue'
</script>
```

### 🏷️ Field Mapping:
Component linh hoạt hỗ trợ cả naming cũ và mới:
```
name/ten_dich_vu     → Tên dịch vụ
category/loai        → Loại dịch vụ
price/gia           → Giá
description/mo_ta   → Mô tả
```

---

## 2️⃣ Component: Xuất/In Hóa Đơn

**Vị trí file:** `src/components/admin/hoaban/BillPrinter.vue`

### ✨ Tính năng chính:
- ✅ **Chọn bàn**: Dropdown danh sách bàn
- ✅ **Hóa đơn chuyên nghiệp**: Thiết kế giống hóa đơn thực
- ✅ **In thermal printer**: CSS @media print tối ưu (80mm)
- ✅ **Xuất PDF**: Ready cho future implementation
- ✅ **Tính toán tự động**: Tổng tiền, số lượng
- ✅ **Thông tin chi tiết**: Bàn, giờ vào/ra, nhân viên
- ✅ **Responsive**: In trên A4, thermal, mobile

### 📦 API Endpoints:
```
GET    /admin/ban/get-data                                  → Lấy danh sách bàn
GET    /admin/hoa-don/get-bill-by-ban-id?ban_id=X          → Lấy hóa đơn theo bàn
```

### 🖨️ Print Features:
- **Thermal Printer (80mm)**: Tối ưu cho máy in nhiệt
- **A4 Paper**: Hỗ trợ in trên giấy A4
- **Auto Layout**: Tự động điều chỉnh nội dung
- **Print Button**: `window.print()` built-in

### 🎨 Receipt Design:
```
┌─────────────────────┐
│   🎱 QUÁN BIDA VIP  │
│  Giải trí, Chất lượng│
│      HÓA ĐƠN        │
├─────────────────────┤
│ Bàn: 5              │
│ Vào: 14:30:00       │
│ Ra:  16:45:30       │
├─────────────────────┤
│ Sản phẩm | SL | Giá │
├─────────────────────┤
│ Nước chanh | 2 | ... │
│ Bánh mì | 1 | ...    │
├─────────────────────┤
│ Tổng cộng: 250.000đ │
└─────────────────────┘
```

### 🔧 Cách dùng:
```vue
<!-- Thêm vào router hoặc component cha -->
<template>
  <BillPrinter />
</template>

<script setup>
import BillPrinter from '@/components/admin/hoaban/BillPrinter.vue'
</script>
```

### 🏷️ Field Mapping:
```
tableNumber/ban_id        → Số bàn
startTime/gio_vao         → Giờ vào
endTime/gio_ra            → Giờ ra
staffName/nhan_vien       → Nhân viên
items[].name/ten_dich_vu  → Tên sản phẩm
items[].price/gia         → Giá sản phẩm
items[].quantity/so_luong → Số lượng
```

---

## ⚙️ Setup & Configuration

### 1. Import trong Router:
```javascript
// router/index.js
import DichVuManagement from '@/components/admin/dichvu/index.vue'
import BillPrinter from '@/components/admin/hoaban/BillPrinter.vue'

const routes = [
  {
    path: '/admin/dich-vu',
    component: DichVuManagement,
    meta: { title: 'Quản lý Dịch vụ' }
  },
  {
    path: '/admin/hoa-don',
    component: BillPrinter,
    meta: { title: 'In Hóa Đơn' }
  }
]
```

### 2. Cấu hình API Base URL:
```javascript
// vite.config.js hoặc .env.local
VITE_API_URL=http://localhost:8000/api
```

### 3. Dependencies cần thiết:
```bash
npm install axios
npm install vue@^3.3.4
npm install vue-router@^4.0.13
```

### 4. (Optional) Enable PDF Export:
```bash
npm install html2pdf.js
# Sau đó uncomment code trong BillPrinter.vue exportPDF()
```

---

## 🎨 Customization Guide

### Đổi màu chính:
```css
:root {
  --primary-color: #3b82f6;      /* Xanh dương */
  --primary-dark: #1e40af;
  --success-color: #10b981;      /* Xanh lá */
  --danger-color: #ef4444;       /* Đỏ */
}
```

### Đổi Border Radius:
```css
--border-radius: 12px;      /* Nhỏ */
--border-radius-lg: 16px;   /* Lớn */
```

### Đổi Shadow:
```css
--box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
--box-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
```

### Custom Receipt Template:
Nhân hiệu cửa hàng: `<h2 class="business-name">🎱 QUÁN BIDA VIP</h2>`

---

## 🔐 Backend Requirements

### API Response Format:

**GET /admin/dich-vu/get-data:**
```json
{
  "data": [
    {
      "id": 1,
      "ten_dich_vu": "Nước chanh",
      "gia": 25000,
      "loai": "Nước uống",
      "mo_ta": "Nước chanh tươi mát"
    }
  ]
}
```

**GET /admin/hoa-don/get-bill-by-ban-id?ban_id=5:**
```json
{
  "data": {
    "ban_id": 5,
    "gio_vao": "2024-03-26 14:30:00",
    "gio_ra": "2024-03-26 16:45:00",
    "nhan_vien": "Nguyễn Văn A",
    "items": [
      {
        "ten_dich_vu": "Nước chanh",
        "gia": 25000,
        "so_luong": 2
      }
    ]
  }
}
```

---

## 📱 Responsive Breakpoints

- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: 480px - 767px
- **Small Mobile**: < 480px

---

## 🐛 Troubleshooting

### Component không load data?
- ✓ Kiểm tra API URL trong `.env.local`
- ✓ Kiểm tra CORS header từ backend
- ✓ Mở DevTools (F12) xem network tab

### In hóa đơn bị cắt?
- ✓ Điều chỉnh: `@page { size: 80mm auto; }`
- ✓ Kiểm tra margin/padding trong print styles

### Modal không đóng sau submit?
- ✓ Kiểm tra `isSubmitting.value` có về false không
- ✓ Xem console có error không

---

## 📚 Best Practices

✅ **Validation**: Form tự động validate khi blur
✅ **Error Handling**: Toàn bộ try-catch + user feedback
✅ **Loading States**: Show spinner khi loading
✅ **Toast Notifications**: Auto hide sau 3s
✅ **Accessibility**: Semantic HTML + keyboard support
✅ **Performance**: Lazy loading, computed properties
✅ **Mobile First**: Responsive design built-in

---

## 🚀 Next Steps

1. **Test API calls** bằng Postman
2. **Customize màu sắc** theo branding quán
3. **(Optional) Enable PDF export** bằng html2pdf
4. **Add authentication** nếu cần
5. **Add print header/footer** tùy chỉnh

---

## 💡 Tips & Tricks

- **Tìm kiếm nhanh**: Ctrl+F trong danh sách
- **In nhiều bàn**: Chọn bàn khác → In ngay
- **Export PDF**: Click "Xuất PDF" (sau khi enable)
- **Mobile printing**: Dùng share → Print PDF
- **Dark mode**: Spring soon! 🌙

---

## 📞 Support

Nếu gặp vấn đề:
1. Kiểm tra console (F12)
2. Xem network requests
3. Verify API responses
4. Check field mappings

**Happy coding! 🎉**
