<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileOrder
 *
 * @ORM\Table(name="file_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileOrderRepository")
 */
class FileOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FileUpload
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FileUpload")
     */
    private $fileUploads;

    /**
     * @var Cart
     * @ORM\Column(type="integer")
     */
    private $cart;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fileUploads
     *
     * @param \AppBundle\Entity\FileUpload $fileUploads
     *
     * @return FileOrder
     */
    public function setFileUploads(\AppBundle\Entity\FileUpload $fileUploads = null)
    {
        $this->fileUploads = $fileUploads;

        return $this;
    }

    /**
     * Get fileUploads
     *
     * @return \AppBundle\Entity\FileUpload
     */
    public function getFileUploads()
    {
        return $this->fileUploads;
    }

    /**
     * Set cart
     *
     *
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     */
    public function getCart()
    {
        return $this->cart;
    }
}
