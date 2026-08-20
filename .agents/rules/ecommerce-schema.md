# Quy Chuẩn CSDL Hệ Thống Thương Mại Điện Tử (TMDT)

## Danh Sách 6 Bảng Cốt Lõi

1. **`categories`**
   - `id`: bigint unsigned, primary key, auto increment.
   - `name`: varchar(255), unique, not null.
   - `timestamps`: created_at, updated_at.

2. **`products`**
   - `id`: bigint unsigned, primary key.
   - `category_id`: foreign key -> categories(id) on delete cascade.
   - `name`: varchar(255), not null.
   - `price`: decimal(12, 2), unsigned, not null.
   - `stock`: integer unsigned, default 0.
   - `description`: text, nullable.
   - `image`: varchar(255), nullable.
   - `timestamps`.

3. **`users`**
   - `id`, `name`, `email` (unique), `password`, `role` (enum: 'admin', 'customer'), `phone`, `address`, `timestamps`.

4. **`orders`**
   - `id`, `user_id` (foreign key -> users), `total_amount` (decimal 12, 2), `status` (enum: 'pending', 'processing', 'completed', 'cancelled'), `shipping_address`, `timestamps`.

5. **`order_items`**
   - `id`, `order_id` (foreign key -> orders on delete cascade), `product_id` (foreign key -> products), `quantity` (int unsigned), `price` (decimal 12, 2), `timestamps`.

6. **`payments`**
   - `id`, `order_id` (foreign key -> orders), `payment_method` (enum: 'cod', 'vnpay', 'momo', 'bank_transfer'), `amount` (decimal 12, 2), `status` (enum: 'pending', 'paid', 'failed'), `transaction_id`, `timestamps`.
