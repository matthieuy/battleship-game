<?php

namespace App\GameHelper;

/**
 * Class Boat
 * It's a type of boat (destroyer)
 */
class Boat
{
    protected $name;
    protected $number;
    protected $img;
    protected $imgDead;

    /**
     * Set name
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set Number
     * @param int $number
     *
     * @return $this
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number of this boat
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * Set Img
     * @param array<int> $img
     *
     * @return $this
     */
    public function setImgAlive(array $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get image number
     * @param int  $case       Case number of the boat
     * @param bool $horizontal is horizontal
     * @param bool $isDead     is boat is dead ?
     *
     * @return int
     */
    public function getImg(int $case, bool $horizontal = true, bool $isDead = true): int
    {
        $horizontalInt = $horizontal ? 1 : 0;
        $imgList = $isDead ? $this->imgDead : $this->img;

        return $imgList[$horizontalInt][$case];
    }

    /**
     * Set ImgDead
     * @param array<int> $imgDead
     *
     * @return $this
     */
    public function setImgDead(array $imgDead): self
    {
        $this->imgDead = $imgDead;

        return $this;
    }

    /**
     * Get the length of the boat
     * @return int
     */
    public function getLength(): int
    {
        return count($this->img[0]);
    }

    /**
     * Get image dead from img alive number
     * @param int $img
     *
     * @return int|null
     */
    public function findDeadImg(int $img): ?int
    {
        for ($orientation = 0; $orientation <= 1; $orientation++) {
            // phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
            foreach ($this->img as $i => $imgNumber) {
                if ($this->img[$orientation][$i] === $img) {
                    return $this->imgDead[$orientation][$i];
                }
            }
        }

        return $img;
    }
}
