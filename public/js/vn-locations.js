/**
 * Vietnam Administrative Divisions Helper (Tỉnh / Thành phố - Quận / Huyện - Phường / Xã)
 * Aurelia TMDT - Enterprise Ecommerce Solution
 */

const VN_LOCATIONS_DATA = [
    {
        name: "Thành phố Hà Nội",
        districts: [
            {
                name: "Quận Ba Đình",
                wards: ["Phường Cống Vị", "Phường Điện Biên", "Phường Đội Cấn", "Phường Giảng Võ", "Phường Kim Mã", "Phường Liễu Giai", "Phường Ngọc Hà", "Phường Ngọc Khánh", "Phường Nguyễn Trung Trực", "Phường Phúc Xá", "Phường Quán Thánh", "Phường Thành Công", "Phường Trúc Bạch", "Phường Vĩnh Phúc"]
            },
            {
                name: "Quận Hoàn Kiếm",
                wards: ["Phường Chương Dương", "Phường Cửa Đông", "Phường Cửa Nam", "Phường Đồng Xuân", "Phường Hàng Bạc", "Phường Hàng Bài", "Phường Hàng Bồ", "Phường Hàng Bông", "Phường Hàng Buồm", "Phường Hàng Đào", "Phường Hàng Gai", "Phường Hàng Mã", "Phường Hàng Trống", "Phường Lý Thái Tổ", "Phường Phan Chu Trinh", "Phường Phúc Tân", "Phường Tràng Tiền", "Phường Trần Hưng Đạo"]
            },
            {
                name: "Quận Tây Hồ",
                wards: ["Phường Bưởi", "Phường Nhật Tân", "Phường Phú Thượng", "Phường Quảng An", "Phường Thụy Khuê", "Phường Tứ Liên", "Phường Xuân La", "Phường Yên Phụ"]
            },
            {
                name: "Quận Cầu Giấy",
                wards: ["Phường Dịch Vọng", "Phường Dịch Vọng Hậu", "Phường Mai Dịch", "Phường Nghĩa Đô", "Phường Nghĩa Tân", "Phường Quan Hoa", "Phường Trung Hòa", "Phường Yên Hòa"]
            },
            {
                name: "Quận Đống Đa",
                wards: ["Phường Cát Linh", "Phường Hàng Bột", "Phường Khâm Thiên", "Phường Khương Thượng", "Phường Kim Liên", "Phường Láng Hạ", "Phường Láng Thượng", "Phường Nam Đồng", "Phường Ngã Tư Sở", "Phường Ô Chợ Dừa", "Phường Phương Liên", "Phường Phương Mai", "Phường Quang Trung", "Phường Quốc Tử Giám", "Phường Thịnh Quang", "Phường Thổ Quan", "Phường Trung Liệt", "Phường Trung Phụng", "Phường Trung Tự", "Phường Văn Chương", "Phường Văn Miếu"]
            },
            {
                name: "Quận Hai Bà Trưng",
                wards: ["Phường Bách Khoa", "Phường Bạch Đằng", "Phường Bạch Mai", "Phường Cầu Dền", "Phường Đống Mác", "Phường Đồng Nhân", "Phường Đồng Tâm", "Phường Lê Đại Hành", "Phường Minh Khai", "Phường Nguyễn Du", "Phường Phạm Đình Hổ", "Phường Phố Huế", "Phường Quỳnh Lôi", "Phường Quỳnh Mai", "Phường Thanh Lương", "Phường Thanh Nhàn", "Phường Trương Định", "Phường Vĩnh Tuy"]
            },
            {
                name: "Quận Hoàng Mai",
                wards: ["Phường Đại Kim", "Phường Định Công", "Phường Giáp Bát", "Phường Hoàng Liệt", "Phường Hoàng Văn Thụ", "Phường Lĩnh Nam", "Phường Mai Động", "Phường Tân Mai", "Phường Thanh Trì", "Phường Thịnh Liệt", "Phường Trần Phú", "Phường Tương Mai", "Phường Vĩnh Hưng", "Phường Yên Sở"]
            },
            {
                name: "Quận Thanh Xuân",
                wards: ["Phường Hạ Đình", "Phường Khương Đình", "Phường Khương Mai", "Phường Khương Trung", "Phường Kim Giang", "Phường Nhân Chính", "Phường Phương Liệt", "Phường Thanh Xuân Bắc", "Phường Thanh Xuân Nam", "Phường Thanh Xuân Trung", "Phường Thượng Đình"]
            },
            {
                name: "Quận Nam Từ Liêm",
                wards: ["Phường Cầu Diễn", "Phường Đại Mỗ", "Phường Mễ Trì", "Phường Mỹ Đình 1", "Phường Mỹ Đình 2", "Phường Phú Đô", "Phường Phương Canh", "Phường Tây Mỗ", "Phường Trung Văn", "Phường Xuân Phương"]
            },
            {
                name: "Quận Bắc Từ Liêm",
                wards: ["Phường Cổ Nhuế 1", "Phường Cổ Nhuế 2", "Phường Đông Ngạc", "Phường Đức Thắng", "Phường Liên Mạc", "Phường Minh Khai", "Phường Phú Diễn", "Phường Phúc Diễn", "Phường Tây Tựu", "Phường Thượng Cát", "Phường Thụy Phương", "Phường Xuân Đỉnh", "Phường Xuân Tảo"]
            },
            {
                name: "Quận Hà Đông",
                wards: ["Phường Biên Giang", "Phường Đồng Mai", "Phường Dương Nội", "Phường Hà Cầu", "Phường Kiến Hưng", "Phường La Khê", "Phường Mộ Lao", "Phường Nguyễn Trãi", "Phường Phú La", "Phường Phú Lãm", "Phường Phú Lương", "Phường Phúc La", "Phường Quang Trung", "Phường Vạn Phúc", "Phường Văn Quán", "Phường Yên Nghĩa", "Phường Yết Kiêu"]
            },
            {
                name: "Quận Long Biên",
                wards: ["Phường Bồ Đề", "Phường Cự Khối", "Phường Đức Giang", "Phường Gia Thụy", "Phường Giang Biên", "Phường Long Biên", "Phường Ngọc Lâm", "Phường Ngọc Thụy", "Phường Phúc Đồng", "Phường Phúc Lợi", "Phường Sài Đồng", "Phường Thạch Bàn", "Phường Thượng Thanh", "Phường Việt Hưng"]
            }
        ]
    },
    {
        name: "Thành phố Hồ Chí Minh",
        districts: [
            {
                name: "Quận 1",
                wards: ["Phường Bến Nghé", "Phường Bến Thành", "Phường Cầu Kho", "Phường Cầu Ông Lãnh", "Phường Cô Giang", "Phường Đa Kao", "Phường Nguyễn Cư Trinh", "Phường Nguyễn Thái Bình", "Phường Phạm Ngũ Lão", "Phường Tân Định"]
            },
            {
                name: "Quận 3",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường Võ Thị Sáu"]
            },
            {
                name: "Quận 4",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 6", "Phường 8", "Phường 9", "Phường 10", "Phường 13", "Phường 14", "Phường 15", "Phường 16", "Phường 18"]
            },
            {
                name: "Quận 5",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14"]
            },
            {
                name: "Quận 7",
                wards: ["Phường Bình Thuận", "Phường Phú Mỹ", "Phường Phú Thuận", "Phường Tân Hưng", "Phường Tân Kiểng", "Phường Tân Phong", "Phường Tân Phú", "Phường Tân Quy", "Phường Tân Thuận Đông", "Phường Tân Thuận Tây"]
            },
            {
                name: "Quận 10",
                wards: ["Phường 1", "Phường 2", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"]
            },
            {
                name: "Quận Bình Thạnh",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 5", "Phường 6", "Phường 7", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 17", "Phường 19", "Phường 21", "Phường 22", "Phường 24", "Phường 25", "Phường 26", "Phường 27", "Phường 28"]
            },
            {
                name: "Quận Phú Nhuận",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 13", "Phường 15", "Phường 17"]
            },
            {
                name: "Quận Tân Bình",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"]
            },
            {
                name: "Thành phố Thủ Đức",
                wards: ["Phường An Khánh", "Phường An Lợi Đông", "Phường An Phú", "Phường Bình Chiểu", "Phường Bình Thọ", "Phường Hiệp Bình Chánh", "Phường Hiệp Bình Phước", "Phường Hiệp Phú", "Phường Linh Chiểu", "Phường Linh Đông", "Phường Linh Tây", "Phường Linh Trung", "Phường Linh Xuân", "Phường Long Bình", "Phường Long Phước", "Phường Long Thạnh Mỹ", "Phường Long Trường", "Phường Phú Hữu", "Phường Phước Bình", "Phường Phước Long A", "Phường Phước Long B", "Phường Tam Bình", "Phường Tam Phú", "Phường Tăng Nhơn Phú A", "Phường Tăng Nhơn Phú B", "Phường Tân Phú", "Phường Thảo Điền", "Phường Thạnh Mỹ Lợi", "Phường Thủ Thiêm", "Phường Trường Thạnh", "Phường Trường Thọ"]
            }
        ]
    },
    {
        name: "Thành phố Đà Nẵng",
        districts: [
            {
                name: "Quận Hải Châu",
                wards: ["Phường Bình Hiên", "Phường Bình Thuận", "Phường Hải Châu I", "Phường Hải Châu II", "Phường Hòa Cường Bắc", "Phường Hòa Cường Nam", "Phường Hòa Thuận Đông", "Phường Hòa Thuận Tây", "Phường Nam Dương", "Phường Phước Ninh", "Phường Thạch Thang", "Phường Thanh Bình", "Phường Thuận Phước"]
            },
            {
                name: "Quận Thanh Khê",
                wards: ["Phường An Khê", "Phường Chính Gián", "Phường Hòa Khê", "Phường Tam Thuận", "Phường Tân Chính", "Phường Thạc Gián", "Phường Thanh Khê Đông", "Phường Thanh Khê Tây", "Phường Vĩnh Trung", "Phường Xuân Hà"]
            },
            {
                name: "Quận Sơn Trà",
                wards: ["Phường An Hải Bắc", "Phường An Hải Đông", "Phường An Hải Tây", "Phường Mân Thái", "Phường Nại Hiên Đông", "Phường Phước Mỹ", "Phường Thọ Quang"]
            },
            {
                name: "Quận Ngũ Hành Sơn",
                wards: ["Phường Hòa Hải", "Phường Hòa Quý", "Phường Khuê Mỹ", "Phường Mỹ An"]
            },
            {
                name: "Quận Liên Chiểu",
                wards: ["Phường Hòa Hiệp Bắc", "Phường Hòa Hiệp Nam", "Phường Hòa Khánh Bắc", "Phường Hòa Khánh Nam", "Phường Hòa Minh"]
            },
            {
                name: "Quận Cẩm Lệ",
                wards: ["Phường Hòa An", "Phường Hòa Phát", "Phường Hòa Thọ Đông", "Phường Hòa Thọ Tây", "Phường Hòa Xuân", "Phường Khuê Trung"]
            }
        ]
    },
    {
        name: "Thành phố Hải Phòng",
        districts: [
            {
                name: "Quận Hồng Bàng",
                wards: ["Phường Hoàng Văn Thụ", "Phường Hùng Vương", "Phường Minh Khai", "Phường Phan Bội Châu", "Phường Quán Toan", "Phường Sở Dầu", "Phường Thượng Lý", "Phường Trại Chuối", "Phường Hạ Lý"]
            },
            {
                name: "Quận Ngô Quyền",
                wards: ["Phường Cầu Đất", "Phường Cầu Tre", "Phường Đằng Giang", "Phường Đông Khê", "Phường Gia Viên", "Phường Lạc Viên", "Phường Lạch Tray", "Phường Lê Lợi", "Phường Máy Chai", "Phường Máy Tơ", "Phường Vạn Mỹ", "Phường Đổng Quốc Bình"]
            },
            {
                name: "Quận Lê Chân",
                wards: ["Phường An Biên", "Phường An Dương", "Phường Cát Dài", "Phường Dư Hàng", "Phường Dư Hàng Kênh", "Phường Hàng Kênh", "Phường Hồ Nam", "Phường Kênh Dương", "Phường Lam Sơn", "Phường Niệm Nghĩa", "Phường Nghĩa Xá", "Phường Trại Cau", "Phường Trần Nguyên Hãn", "Phường Vĩnh Niệm"]
            },
            {
                name: "Quận Hải An",
                wards: ["Phường Cát Bi", "Phường Đằng Hải", "Phường Đằng Lâm", "Phường Đông Hải 1", "Phường Đông Hải 2", "Phường Nam Hải", "Phường Thành Tô", "Phường Tràng Cát"]
            }
        ]
    },
    {
        name: "Thành phố Cần Thơ",
        districts: [
            {
                name: "Quận Ninh Kiều",
                wards: ["Phường An Bình", "Phường An Cư", "Phường An Hòa", "Phường An Khánh", "Phường An Nghiệp", "Phường An Phú", "Phường Cái Khế", "Phường Hưng Lợi", "Phường Tân An", "Phường Thới Bình", "Phường Xuân Khánh"]
            },
            {
                name: "Quận Cái Răng",
                wards: ["Phường Ba Láng", "Phường Hưng Phú", "Phường Hưng Thạnh", "Phường Lê Bình", "Phường Phú Thứ", "Phường Tân Phú", "Phường Thường Thạnh"]
            },
            {
                name: "Quận Bình Thủy",
                wards: ["Phường An Thới", "Phường Bình Thủy", "Phường Bùi Hữu Nghĩa", "Phường Long Hòa", "Phường Long Tuyền", "Phường Thới An Đông", "Phường Trà An", "Phường Trà Nóc"]
            }
        ]
    },
    {
        name: "Tỉnh Bình Dương",
        districts: [
            {
                name: "Thành phố Thủ Dầu Một",
                wards: ["Phường Chánh Mỹ", "Phường Chánh Nghĩa", "Phường Định Hòa", "Phường Hiệp An", "Phường Hiệp Thành", "Phường Hòa Phú", "Phường Phú Cường", "Phường Phú Hòa", "Phường Phú Lợi", "Phường Phú Mỹ", "Phường Phú Tân", "Phường Phú Thọ", "Phường Tân An", "Phường Tương Bình Hiệp"]
            },
            {
                name: "Thành phố Thuận An",
                wards: ["Phường An Phú", "Phường An Thạnh", "Phường Bình Chuẩn", "Phường Bình Hòa", "Phường Bình Nhâm", "Phường Hưng Định", "Phường Lái Thiêu", "Phường Thuận Giao", "Phường Vĩnh Phú", "Xã An Sơn"]
            },
            {
                name: "Thành phố Dĩ An",
                wards: ["Phường An Bình", "Phường Bình An", "Phường Bình Thắng", "Phường Dĩ An", "Phường Đông Hòa", "Phường Tân Bình", "Phường Tân Đông Hiệp"]
            }
        ]
    },
    {
        name: "Tỉnh Đồng Nai",
        districts: [
            {
                name: "Thành phố Biên Hòa",
                wards: ["Phường An Bình", "Phường An Hòa", "Phường Bình Đa", "Phường Bửu Hòa", "Phường Bửu Long", "Phường Hiệp Hòa", "Phường Hóa An", "Phường Hòa Bình", "Phường Hố Nai", "Phường Long Bình", "Phường Long Bình Tân", "Phường Phước Tân", "Phường Quang Vinh", "Phường Quyết Thắng", "Phường Tam Hiệp", "Phường Tam Hòa", "Phường Tam Phước", "Phường Tân Biên", "Phường Tân Hạnh", "Phường Tân Hòa", "Phường Tân Hiệp", "Phường Tân Mai", "Phường Tân Phong", "Phường Tân Tiến", "Phường Tân Vạn", "Phường Thanh Bình", "Phường Thống Nhất", "Phường Trảng Dài", "Phường Trung Dũng"]
            },
            {
                name: "Huyện Long Thành",
                wards: ["Thị trấn Long Thành", "Xã An Phước", "Xã Bàu Cạn", "Xã Bình An", "Xã Cẩm Đường", "Xã Lộc An", "Xã Long An", "Xã Long Đức", "Xã Phước Bình", "Xã Phước Thái", "Xã Tam An", "Xã Tân Hiệp"]
            }
        ]
    },
    {
        name: "Tỉnh Quảng Ninh",
        districts: [
            {
                name: "Thành phố Hạ Long",
                wards: ["Phường Bạch Đằng", "Phường Bãi Cháy", "Phường Cao Thắng", "Phường Cao Xanh", "Phường Đại Yên", "Phường Giếng Đáy", "Phường Hà Khánh", "Phường Hà Khẩu", "Phường Hà Lầm", "Phường Hà Phong", "Phường Hà Trung", "Phường Hà Tu", "Phường Hoành Bồ", "Phường Hồng Gai", "Phường Hồng Hà", "Phường Hồng Hải", "Phường Hùng Thắng", "Phường Tuần Châu", "Phường Việt Hưng", "Phường Yết Kiêu"]
            }
        ]
    },
    {
        name: "Tỉnh Thừa Thiên Huế",
        districts: [
            {
                name: "Thành phố Huế",
                wards: ["Phường An Cựu", "Phường An Đông", "Phường An Hòa", "Phường An Tây", "Phường Đông Ba", "Phường Gia Hội", "Phường Hương An", "Phường Hương Hồ", "Phường Hương Long", "Phường Hương Sơ", "Phường Hương Vinh", "Phường Kim Long", "Phường Phú Hậu", "Phường Phú Hội", "Phường Phú Nhuận", "Phường Phú Thượng", "Phường Phước Vĩnh", "Phường Phường Đúc", "Phường Tây Lộc", "Phường Thuận An", "Phường Thuận Hòa", "Phường Thuận Lộc", "Phường Thủy Biều", "Phường Thủy Vân", "Phường Thủy Xuân", "Phường Vĩnh Ninh", "Phường Vỹ Dạ", "Phường Xuân Phú"]
            }
        ]
    },
    {
        name: "Tỉnh Khánh Hòa",
        districts: [
            {
                name: "Thành phố Nha Trang",
                wards: ["Phường Lộc Thọ", "Phường Ngọc Hiệp", "Phường Phước Hải", "Phường Phước Hòa", "Phường Phước Long", "Phường Phước Tân", "Phường Phước Tiến", "Phường Phương Sài", "Phường Phương Sơn", "Phường Tân Lập", "Phường Vạn Thạnh", "Phường Vạn Thắng", "Phường Vĩnh Hải", "Phường Vĩnh Hòa", "Phường Vĩnh Phước", "Phường Vĩnh Thọ", "Phường Vĩnh Trường", "Phường Vĩnh Nguyên", "Phường Xương Huân"]
            }
        ]
    },
    {
        name: "Tỉnh Lâm Đồng",
        districts: [
            {
                name: "Thành phố Đà Lạt",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Xã Tà Nung", "Xã Trạm Hành", "Xã Xuân Thọ", "Xã Xuân Trường"]
            }
        ]
    },
    {
        name: "Tỉnh Nghệ An",
        districts: [
            {
                name: "Thành phố Vinh",
                wards: ["Phường Bến Thủy", "Phường Cửa Nam", "Phường Đội Cung", "Phường Đông Vĩnh", "Phường Hà Huy Tập", "Phường Hưng Bình", "Phường Hưng Dũng", "Phường Hưng Phúc", "Phường Lê Lợi", "Phường Lê Mao", "Phường Quán Bàu", "Phường Quang Trung", "Phường Trung Đô", "Phường Trường Thi", "Phường Vinh Tân"]
            }
        ]
    },
    {
        name: "Tỉnh Thanh Hóa",
        districts: [
            {
                name: "Thành phố Thanh Hóa",
                wards: ["Phường Ba Đình", "Phường Điện Biên", "Phường Đông Cương", "Phường Đông Hải", "Phường Đông Hương", "Phường Đông Sơn", "Phường Đông Thọ", "Phường Đông Vệ", "Phường Hàm Rồng", "Phường Lam Sơn", "Phường Nam Ngạn", "Phường Ngọc Trạo", "Phường Phú Sơn", "Phường Quảng Cát", "Phường Quảng Đông", "Phường Quảng Hưng", "Phường Quảng Tâm", "Phường Quảng Thành", "Phường Quảng Thắng", "Phường Quảng Thịnh", "Phường Tào Xuyên", "Phường Tân Sơn", "Phường Trường Thi"]
            }
        ]
    },
    {
        name: "Tỉnh Bắc Ninh",
        districts: [
            {
                name: "Thành phố Bắc Ninh",
                wards: ["Phường Đáp Cầu", "Phường Thị Cầu", "Phường Vũ Ninh", "Phường Suối Hoa", "Phường Tiền An", "Phường Ninh Xá", "Phường Vệ An", "Phường Kinh Bắc", "Phường Đại Phúc", "Phường Võ Cường", "Phường Vân Dương", "Phường Hắp Lĩnh", "Phường Phong Khê", "Phường Kim Chân", "Phường Nam Sơn", "Phường Khắc Niệm"]
            },
            {
                name: "Thành phố Từ Sơn",
                wards: ["Phường Châu Khê", "Phường Đình Bảng", "Phường Đồng Kỵ", "Phường Đông Ngàn", "Phường Đồng Nguyên", "Phường Hương Mạc", "Phường Phù Chẩn", "Phường Phù Khê", "Phường Tam Sơn", "Phường Tân Hồng", "Phường Trang Hạ", "Phường Tương Giang"]
            }
        ]
    },
    {
        name: "Tỉnh Bà Rịa - Vũng Tàu",
        districts: [
            {
                name: "Thành phố Vũng Tàu",
                wards: ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường Thắng Nhất", "Phường Thắng Nhì", "Phường Thắng Tam", "Phường Nguyễn An Ninh", "Phường Rạch Dừa", "Xã Long Sơn"]
            }
        ]
    },
    {
        name: "Tỉnh Kiên Giang",
        districts: [
            {
                name: "Thành phố Phú Quốc",
                wards: ["Phường Dương Đông", "Phường An Thới", "Xã Bãi Thơm", "Xã Cửa Cạn", "Xã Cửa Dương", "Xã Dương Tơ", "Xã Gành Dầu", "Xã Hàm Ninh", "Xã Thổ Châu"]
            }
        ]
    }
];

// Asynchronously load full 63 provinces from GHN API via internal backend cache, otherwise fallback to built-in dataset
let fullVnLocations = VN_LOCATIONS_DATA;

/**
 * Initialize 3 cascading select dropdowns for Province, District, Ward
 * Prioritizes GHN Master Data API via /api/shipping/... with instant fallback to static dataset
 */
async function initVnLocationSelects(provinceElId, districtElId, wardElId, initialData = {}) {
    const provinceSelect = typeof provinceElId === 'string' ? document.getElementById(provinceElId) : provinceElId;
    const districtSelect = typeof districtElId === 'string' ? document.getElementById(districtElId) : districtElId;
    const wardSelect = typeof wardElId === 'string' ? document.getElementById(wardElId) : wardElId;

    if (!provinceSelect || !districtSelect || !wardSelect) return;

    let useGhnApi = true;

    // 1. Populate Provinces
    provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
    try {
        const res = await fetch('/api/shipping/provinces');
        const json = await res.json();
        if (json.success && Array.isArray(json.data) && json.data.length > 0) {
            json.data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.ProvinceName;
                opt.dataset.id = p.ProvinceID;
                opt.textContent = p.ProvinceName;
                if (initialData.province && (p.ProvinceName === initialData.province || p.ProvinceName.includes(initialData.province))) {
                    opt.selected = true;
                }
                provinceSelect.appendChild(opt);
            });
        } else {
            useGhnApi = false;
            fallbackPopulateProvinces();
        }
    } catch (e) {
        useGhnApi = false;
        fallbackPopulateProvinces();
    }

    function fallbackPopulateProvinces() {
        fullVnLocations.forEach(prov => {
            const opt = document.createElement('option');
            opt.value = prov.name;
            opt.textContent = prov.name;
            if (initialData.province && (prov.name === initialData.province || prov.name.includes(initialData.province))) {
                opt.selected = true;
            }
            provinceSelect.appendChild(opt);
        });
    }

    // 2. Populate Districts
    async function populateDistricts(selectedProvinceName, selectedDistrictName = '') {
        districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        if (!selectedProvinceName) {
            districtSelect.disabled = true;
            wardSelect.disabled = true;
            return;
        }

        const selectedOpt = provinceSelect.options[provinceSelect.selectedIndex];
        const provinceId = selectedOpt?.dataset?.id;

        if (useGhnApi && provinceId) {
            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Đang tải Quận / Huyện...</option>';
            try {
                const res = await fetch(`/api/shipping/districts?province_id=${provinceId}`);
                const json = await res.json();
                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                if (json.success && Array.isArray(json.data) && json.data.length > 0) {
                    json.data.forEach(dist => {
                        const opt = document.createElement('option');
                        opt.value = dist.DistrictName;
                        opt.dataset.id = dist.DistrictID;
                        opt.textContent = dist.DistrictName;
                        if (selectedDistrictName && (dist.DistrictName === selectedDistrictName || dist.DistrictName.includes(selectedDistrictName))) {
                            opt.selected = true;
                        }
                        districtSelect.appendChild(opt);
                    });
                    districtSelect.disabled = false;
                    return;
                }
            } catch (e) {
                // Fallback below
            }
        }

        // Static Fallback for districts
        districtSelect.disabled = false;
        const province = fullVnLocations.find(p => p.name === selectedProvinceName || p.name.includes(selectedProvinceName));
        if (province && province.districts) {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            province.districts.forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist.name;
                opt.textContent = dist.name;
                if (selectedDistrictName && (dist.name === selectedDistrictName || dist.name.includes(selectedDistrictName))) {
                    opt.selected = true;
                }
                districtSelect.appendChild(opt);
            });
        }
    }

    // 3. Populate Wards
    async function populateWards(selectedProvinceName, selectedDistrictName, selectedWardName = '') {
        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        if (!selectedProvinceName || !selectedDistrictName) {
            wardSelect.disabled = true;
            return;
        }

        const selectedOpt = districtSelect.options[districtSelect.selectedIndex];
        const districtId = selectedOpt?.dataset?.id;

        if (useGhnApi && districtId) {
            wardSelect.disabled = true;
            wardSelect.innerHTML = '<option value="">Đang tải Phường / Xã...</option>';
            try {
                const res = await fetch(`/api/shipping/wards?district_id=${districtId}`);
                const json = await res.json();
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                if (json.success && Array.isArray(json.data) && json.data.length > 0) {
                    json.data.forEach(ward => {
                        const opt = document.createElement('option');
                        opt.value = ward.WardName;
                        opt.dataset.code = ward.WardCode;
                        opt.textContent = ward.WardName;
                        if (selectedWardName && (ward.WardName === selectedWardName || ward.WardName.includes(selectedWardName))) {
                            opt.selected = true;
                        }
                        wardSelect.appendChild(opt);
                    });
                    wardSelect.disabled = false;
                    return;
                }
            } catch (e) {
                // Fallback below
            }
        }

        // Static Fallback for wards
        wardSelect.disabled = false;
        const province = fullVnLocations.find(p => p.name === selectedProvinceName || p.name.includes(selectedProvinceName));
        if (province && province.districts) {
            const district = province.districts.find(d => d.name === selectedDistrictName || d.name.includes(selectedDistrictName));
            if (district && district.wards) {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                district.wards.forEach(wardName => {
                    const opt = document.createElement('option');
                    opt.value = wardName;
                    opt.textContent = wardName;
                    if (selectedWardName && (wardName === selectedWardName || wardName.includes(selectedWardName))) {
                        opt.selected = true;
                    }
                    wardSelect.appendChild(opt);
                });
            }
        }
    }

    // Populate initial values if provided
    if (initialData.province) {
        await populateDistricts(initialData.province, initialData.district || '');
        if (initialData.district) {
            await populateWards(initialData.province, initialData.district, initialData.ward || '');
        }
    } else {
        districtSelect.disabled = true;
        wardSelect.disabled = true;
    }

    // Event handlers
    provinceSelect.onchange = async function () {
        await populateDistricts(this.value);
        if (typeof initialData.onChange === 'function') initialData.onChange();
    };

    districtSelect.onchange = async function () {
        await populateWards(provinceSelect.value, this.value);
        if (typeof initialData.onChange === 'function') initialData.onChange();
    };

    wardSelect.onchange = function () {
        if (typeof initialData.onChange === 'function') initialData.onChange();
    };
}

