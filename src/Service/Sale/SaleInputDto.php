<?php

namespace App\Service\Sale;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SaleInputDto
{

    #[Groups(['beneficiario:create'])]
    #[Assert\NotBlank()]
    public \DateTimeInterface $saleDate;
    


    public function __construct() {}
}
