<?php


class product
{
    /**
     * @var int
     */
    private int $productId;
    /**
     * @var string
     */
    private string $productName;
    /**
     * @var float
     */
    private float $amount;
    /**
     * @var string
     */
    private string $unitOfMeasure;
    /**
     * @var float
     */
    private float $netPrice;
    /**
     * @var int
     */
    private int $tax;
    /**
     * @var string
     */
    private string $photo;

    /**
     * product constructor.
     * @param $productId
     * @throws Exception
     */
    public function __construct($productId)
    {
        if($productId != NULL) {
            $this->getProductFromDataBase($productId);
            $this->setProductId($productId);
        }
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getUnitOfMeasure(): string
    {
        return $this->unitOfMeasure;
    }

    /**
     * @param string $unitOfMeasure
     */
    public function setUnitOfMeasure(string $unitOfMeasure): void
    {
        $this->unitOfMeasure = $unitOfMeasure;
    }

    /**
     * @param int $productId
     * @throws Exception
     */
    private function getProductFromDataBase(int $productId): void
    {
        require_once "connect.php";
        global $db;

        $query = $db->prepare("SELECT * FROM products WHERE productId = :productId");
        $query->bindValue(':productId', $productId, PDO::PARAM_STR);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        if($query->rowCount() != 0) {
            $this->setAmount($result['amount']);
            $this->setProductName($result['productName']);
            $this->setUnitOfMeasure($result['unitOfMeasure']);
            $this->setNetPrice($result['netPrice']);
            $this->setTax($result['tax']);
            $this->setPhoto($result['photo']);
        } else {
            throw new Exception("nie znaleziono produktu");
        }
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo): void
    {
        if($photo == NULL) {
            $photo = "no_photo.jpg";
        }
        $this->photo = $photo;
    }

    /**
     * @return float
     */
    public function getNetPrice(): float
    {
        return $this->netPrice ? $this->netPrice : 0.00;
    }

    /**
     * @param float $netPrice
     */
    public function setNetPrice(float $netPrice): void
    {
        $this->netPrice = $netPrice;
    }

    /**
     * @return int
     */
    public function getTax(): int
    {
        return $this->tax;
    }

    /**
     * @param int $tax
     */
    public function setTax(int $tax): void
    {
        $this->tax = $tax;
    }

    public function getAsRow(): string
    {
        require_once "functions.php";
        return '<td>' . $this->getProductName() . "</td>
                <td>" . $this->getNetPrice() . "</td>
                <td>" . $this->getTax(). "</td>
                <td>" . $this->getGrossPrice() . "</td>
                <td>" . $this->getAmount() . " " . $this->getUnitOfMeasure() . "</td>";
    }

    /**
     * @return float
     */
    public function getGrossPrice(): float
    {
        require_once "functions.php";
        return brutto($this->getNetPrice(), $this->getTax());
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     * @throws Exception
     */
    public function setProductId(int $productId): void
    {
        $this->getProductFromDataBase($productId);
        $this->productId = $productId;
    }

    public static function validName($productName): bool {
        return (is_string($productName) && strlen($productName) > 3 && strlen($productName) < 100);
    }

    public static function validTax($tax): bool {
        return (is_int($tax) && $tax >= 0);
    }

    public static function validNetPrice($netPrice): bool {
        return (is_numeric($netPrice) && $netPrice >= 0);
    }

    public static function validAmount($amount): bool {
        return self::validNetPrice($amount);
    }

    public static function validUnitOfMeasure($unitOfMeasure): bool
    {
        return is_string($unitOfMeasure);
    }

    public static function validPhoto($photo): bool
    {
        $format = ["jpg" , "jpeg", "png"];

        $photo_format = explode(".", $photo['name']);
        $photo_format = end($photo_format);

        return (!is_array($photo) || $photo['error'] !== 0 || !in_array($photo_format, $format) || $photo['size'] > 200000);
    }

    public static function validAll($productName, $netPrice, $tax, $amount, $unitOfMeasure): bool
    {
        return (self::validName($productName) && self::validNetPrice($netPrice) && self::validTax($tax) && self::validAmount($amount) && self::validUnitOfMeasure($unitOfMeasure));
    }

    public function validProduct(): bool
    {
        return self::validAll(
            $this->getProductName(),
            $this->getNetPrice(),
            $this->getTax(),
            $this->getAmount(),
            $this->getUnitOfMeasure());
    }

    public function updateProductInDatabase(): bool
    {
       if(!$this->validProduct()) return false;

        require_once "connect.php";
        global $db;
        $query = $db->prepare('UPDATE products SET productName = :productName, netPrice = :netPrice, tax = :tax, amount = :amount, unitOfMeasure = :uom, photo = :photo WHERE productId = :productId');
        $query-> bindValue(':productName', $this->productName, PDO::PARAM_STR);
        $query-> bindValue(':netPrice', $this->netPrice, PDO::PARAM_STR);
        $query-> bindValue(':tax', $this->tax, PDO::PARAM_INT);
        $query-> bindValue(':amount', $this->amount, PDO::PARAM_STR);
        $query-> bindValue(':uom', $this->unitOfMeasure, PDO::PARAM_STR);
        $query-> bindValue(':photo', $this->photo, PDO::PARAM_STR);
        $query-> bindValue(':productId', $this->productId, PDO::PARAM_INT);
        $query->execute();

        return true;
    }

    public function checkName(): bool
    {
       return self::validName($this->getProductName());
    }

    public function checkTax(): bool
    {
       return self::validTax($this->getTax());
    }

    public function checkNetPrice(): bool
    {
       return self::validNetPrice($this->getNetPrice());
    }

    public function checkAmount(): bool
    {
       return self::validAmount($this->getAmount());
    }

    public function checkUnitOfMeasure(): bool
    {
        return self::validUnitOfMeasure($this->getUnitOfMeasure());
    }
}