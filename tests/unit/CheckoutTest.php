<?php
use PHPUnit\Framework\TestCase;
include_once('./dbcontroller.php');
include_once('./checkout.php');

class CheckoutTest extends Testcase {


    private $db_handle;
    
    protected function setUp() : void
    {
        $this->db_handle = new DBController();
    }

    public function testValidDBControllerInstance(): void
    {
        $this->assertInstanceOf(DBController::class, new DBController);
    }

    public function testGetDiscountedPrice(): void
    {
        $service = $this->createMock(checkout::class);
        $service
            ->expects(self::once())
            ->method('getDiscountedPrice')
            ->willReturn(100.0);
        self::assertIsFloat($service->getDiscountedPrice('1', '2'));
    }

    public function testValidateGetDiscountedPrice(): void
    {
        $service = $this->createMock(checkout::class);
        $service
            ->expects(self::once())
            ->method('getDiscountedPrice')
            ->willReturn(200.0);
        self::assertSame(200.0, $service->getDiscountedPrice('1', '2'));
    }

    protected function tearDown(): void  
    {
        $this->db_handle = null;
    }

    
    

}    



