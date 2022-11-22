SELECT CONCAT('$ ', FORMAT(t1.st + t1.si - t1.sd - t1.sda, 2)) AS total_entregado FROM (
SELECT SUM(total) AS st, SUM(discount) AS sd, SUM(discount_admin) AS sda, SUM(iva) AS si
FROM orders
WHERE YEAR(delivered_date) = 2021
AND (
    warranty_date IS NULL OR
    warranty_date > delivered_date
)
AND status <> 'cancelado') AS t1
