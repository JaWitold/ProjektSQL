<?php


class invoice
{
    /**
     * @var int
     */
    private int $invoiceId;
    /**
     * @var int
     */
    private int $clientId;
    /**
     * @var int
     */
    private int $recipientId;
    /**
     * @var DateTime
     */
    private DateTime $dateOfIssue;
    /**
     * @var DateTime
     */
    private DateTime $dateOfDelivery;
    /**
     * @var DateTime
     */
    private DateTime $dateOfPayment;
    /**
     * @var string
     */
    private string $paymentType;
    /**
     * @var string
     */
    private string $status;
    /**
     * @var bool
     */
    private bool $isOriginal;
    /**
     * @var float
     */
    private float $sumOfPayment;
    private array $products;

    public function __construct(int $id) {
        $this->getInvoiceFromDatabase($id);
    }

    function __destruct()
    {
        unset($this->invoiceId);
        unset($this->clientId);
        unset($this->recipientId);
        unset($this->products);
        unset($this->products);
        unset($this->products);
        unset($this->products);
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function addProductToInvoice($product): void
    {
        array_push($this->products, $product);
    }

    private function getInvoiceFromDatabase(int $id): void
    {
        require_once "../connect.php";

        global $db;
        $query = $db->prepare("SELECT DISTINCT * FROM invoices WHERE invoiceId = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if($query->rowCount() !== 0) {
            $this->setClientId($result['clientId']);
            $this->setRecipientId($result['repicientId']);
            $this->setDateOfDelivery(DateTime::createFromFormat("Y-m-d", $result['dateOfDelivaery']));
            $this->setDateOfPayment(DateTime::createFromFormat("Y-m-d", $result['dateOfPayment']));
            $this->setDateOfIssue(DateTime::createFromFormat("Y-m-d", $result['dateOfIssue']));
            $this->setStatus($result['status']);
            $this->setPaymentType($result['paymentType']);
            $this->setIsOriginal($result['isOriginal']);

            $query = $db->prepare("SELECT * FROM sold as s JOIN products p on p.productId = s.productId WHERE s.invoiceId = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            foreach ($result as $r){
                require_once "product/product.php";
                $tmpProduct = new product($r['productId']);
                $tmpProduct->setAmount($r['amount']);
                $tmpProduct->setNetPrice($r['netPrice']);
                $tmpProduct->setTax($r['tax']);
                $this->addProductToInvoice($tmpProduct);
            }
        } else {
            echo "nie znaleziono faktury";
            echo '<a href="../login.php">powrót do strony głównej</a>';
            exit();
        }
    }

    /**
     * @return string
     */
    public function showInvoice(): string
    {
        //TODO: wypisywanie faktury;
    }

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    /**
     * @param int $invoiceId
     */
    public function setInvoiceId(int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @return DateTime
     */
    public function getDateOfIssue(): DateTime
    {
        return $this->dateOfIssue;
    }

    /**
     * @param DateTime $dateOfIssue
     */
    public function setDateOfIssue(DateTime $dateOfIssue): void
    {
        $this->dateOfIssue = $dateOfIssue;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return int
     */
    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    /**
     * @param int $recipientId
     */
    public function setRecipientId(int $recipientId): void
    {
        $this->recipientId = $recipientId;
    }

    /**
     * @return DateTime
     */
    public function getDateOfDelivery(): DateTime
    {
        return $this->dateOfDelivery;
    }

    /**
     * @param DateTime $dateOfDelivery
     */
    public function setDateOfDelivery(DateTime $dateOfDelivery): void
    {
        $this->dateOfDelivery = $dateOfDelivery;
    }

    /**
     * @return DateTime
     */
    public function getDateOfPayment(): DateTime
    {
        return $this->dateOfPayment;
    }

    /**
     * @param DateTime $dateOfPayment
     */
    public function setDateOfPayment(DateTime $dateOfPayment): void
    {
        $this->dateOfPayment = $dateOfPayment;
    }

    /**
     * @return string
     */
    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     */
    public function setPaymentType(string $paymentType): void
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isOriginal(): bool
    {
        return $this->isOriginal;
    }

    /**
     * @param bool $isOriginal
     */
    public function setIsOriginal(bool $isOriginal): void
    {
        $this->isOriginal = $isOriginal;
    }

    /**
     * @return float
     */
    public function getSumOfPayment(): float
    {
        return $this->sumOfPayment;
    }

    /**
     * @param float $sumOfPayment
     */
    public function setSumOfPayment(float $sumOfPayment): void
    {
        $this->sumOfPayment = $sumOfPayment;
    }

}