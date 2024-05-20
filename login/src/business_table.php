<?php

include("conn.php");
$sql1 = "CREATE TABLE IF NOT EXISTS business_table (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  location VARCHAR(255) NOT NULL,
  VAT_Number INT NOT NULL,
  Category VARCHAR(255) NOT NULL,
  website VARCHAR(255) NOT NULL
);";

    if($conn->query($sql1)) {
    }else {
      echo "vaag";
    }
$sql2 = "CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
 if($conn->query($sql2)) {
}else {
  echo "vaag";
}

 $sql3 = "CREATE TABLE IF NOT EXISTS invoice_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  item VARCHAR(255) NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (invoice_id) REFERENCES invoices(id)
);";
 if($conn->query($sql3)) {
}else {
  echo "vaag";
}