<?php

namespace App\Entity;

use App\Repository\AttachmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;


#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
class Attachment
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFile = null;

    #[ORM\ManyToOne(inversedBy: 'attachment')]
    private ?Question $question;

    #[ORM\Column(length: 255)]
    private ?string $attachment = null;


    public function getId(): ?int
    {
        return $this->id;
    }



    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAttachments(): ?array
    {
        $array = [
           'val1' => 'test1',
            'val2' => 'test2',
        ];
       return $array;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(string $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }
}
