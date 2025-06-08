## Product category parser

### Installation 

```shell
make install
```

### Run scrape command

```shell
docker compose exec app bin/console app:scrape-products
```
Scrapes first three pages of products category. Use optional arguments (start, end) to modify.


### Endpoint to get paginated list of products

```text
http://localhost:8080/products/1
```
To get next page increase the page counter at the end of the url.

### Products exported in csv format

```text
/export/products.csv
```
