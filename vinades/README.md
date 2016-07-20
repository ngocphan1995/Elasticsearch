Gi chú phát triển elasticsearch

Cấu hình tường lửa của máy elasticsearch Cho các IP:
```
firewall-cmd --permanent --zone=elasticsearch --add-source=10.0.0.28
firewall-cmd --permanent --zone=elasticsearch --add-source=10.0.0.126
firewall-cmd --permanent --zone=elasticsearch --add-source=10.0.0.111
firewall-cmd --reload
```

1) Check out kho code

2) Import CSDL vào để chạy được site

3) Thư kết nối với ES

3) Chạy site:
http://localhost/elasticsearch/admin/
Tài khoản: admin
Mật khẩu: thao@Vinades2016

Huớng dẫn cài đặt:

1) Cập nhật các file thay đổi lên website

2) Thêm đoạn code sau vào file config.php

//Cấu hình host elasticsearch
$db_config['elas_host'] = '10.0.0.124'; 
$db_config['elas_port'] = '9200';

Trong đó 10.0.0.124 là địa chỉ IP của máy chủ elasticsearch
9200 là cổng kết nối tới elasticsearch






