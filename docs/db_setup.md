
Log in to MySQL as root

Change database
```sql
use mysql
```


Update the root password:
```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'eticketing';
```

Flush privileges to apply changes:

```sql
FLUSH PRIVILEGES;
```
Exit MySQL:

```sql
EXIT;
```