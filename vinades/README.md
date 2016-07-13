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



