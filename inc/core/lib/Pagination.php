<?php

/**
 * Pagination: The simplest PHP pagination class
 *
 * @author Leonard Shtika <leonard@shtika.info>
 * @link http://leonard.shtika.info
 * @copyright (C) Leonard Shtika
 * @license MIT.
 */

namespace Inc\Core\Lib;

class Pagination
{
    private $currentPage;
    private $totalRecords;
    private $recordsPerPage;
    private $url;

    public function __construct($currentPage = 1, $totalRecords = 0, $recordsPerPage = 10, $url = '?page=%d')
    {
        $this->currentPage = (int)$currentPage;
        $this->totalRecords = (int)$totalRecords;
        $this->recordsPerPage = (int)$recordsPerPage;
        $this->url = $url;
    }

    /**
     * Calculate offset
     * @return int
     * Example: for 10 recores per page
     * page 1 has offset 0
     * page 2 has offset 10
     */
    public function offset()
    {
        return ($this->currentPage - 1) * $this->recordsPerPage;
    }

    /**
     * Get the records per page
     * @return int
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * Calculate total pages
     */
    private function totalPages()
    {
        return ceil($this->totalRecords / $this->recordsPerPage);
    }

    /**
     * Calculate previous page
     * @return int
     */
    private function previousPage()
    {
        return $this->currentPage - 1;
    }

    /**
     * Calculate next page
     * @return int
     */
    private function nextPage()
    {
        return $this->currentPage + 1;
    }

    /**
     * Check if there is a previous page
     * @return boolean
     */
    private function hasPreviousPage()
    {
        return ($this->previousPage() >= 1) ? true : false;
    }

    /**
     * Check if there is a next page
     * @return boolean
     */
    private function hasNextPage()
    {
        return ($this->nextPage() <= $this->totalPages()) ? true : false;
    }

    /**
     * Generate navigation
     * @param string $type ('pagination' or 'pager')
     * @param int $maxLinks
     * @return mixed (string or false)
     */
    public function nav($type = 'pagination', $maxLinks = 10)
    {
        if ($this->totalPages() > 1) {
            $filename = htmlspecialchars(pathinfo($_SERVER["SCRIPT_FILENAME"], PATHINFO_BASENAME), ENT_QUOTES, "utf-8");

            $links = '<nav class="text-center">';
            $links .= '<ul class="' . $type . '">';

            if ($this->hasPreviousPage()) {
                if ($type == 'pagination') {
                    $links .= '<li><a href="' . sprintf($this->url, 1) . '">&laquo;</a></li>';
                    $links .= '<li><a href="' . sprintf($this->url, $this->previousPage()) . '">-</a></li>';
                } else {
                    $links .= '<li class="previous"><a href="' . sprintf($this->url, $this->previousPage())
                        . '">&larr;</a></li>';
                }
            } else {
                if ($type == 'pagination') {
                    $links .= '<li class="disabled"><a href="#">&laquo;</a></li>';
                    $links .= '<li class="disabled"><a href="#">-</a></li>';
                } else {
                    $links .= '<li class="previous disabled"><a href="#">&larr;</a></li>';
                }
            }


            // Create links in the middle
            if ($type == 'pagination') {
                // Total links
                $totalLinks = ($this->totalPages() <= $maxLinks) ? $this->totalPages() : $maxLinks;

                // Middle link
                $middleLink = floor($totalLinks / 2);

                // Find first link and last link
                if ($this->currentPage <= $middleLink) {
                    $lastLink = $totalLinks;
                    $firstLink = 1;
                } else {
                    if (($this->currentPage + $middleLink) <= $this->totalPages()) {
                        $lastLink = $this->currentPage + $middleLink;
                    } else {
                        $lastLink = $this->totalPages();
                    }

                    $firstLink = $lastLink - $totalLinks + 1;
                }

                for ($i = $firstLink; $i <= $lastLink; $i++) {
                    if ($this->currentPage == $i) {
                        $links .= '<li class="active"><a href="#">' . $i . '</a></li>';
                    } else {
                        $links .= '<li><a href="' . sprintf($this->url, $i) . '">' . $i . '</a></li>';
                    }
                }
            }

            if ($this->hasNextPage()) {
                if ($type == 'pagination') {
                    $links .= '<li><a href="' . sprintf($this->url, $this->nextPage()) . '">+</a></li>';
                    $links .= '<li><a href="' . sprintf($this->url, $this->totalPages()) . '">&raquo;</a></li>';
                } else {
                    $links .= '<li class="next"><a href="' . sprintf($this->url, $this->nextPage())
                        . '">&rarr;</a></li>';
                }
            } else {
                if ($type == 'pagination') {
                    $links .= '<li class="disabled"><a href="#">+</a></li>';
                    $links .= '<li class="disabled"><a href="#">&raquo;</a></li>';
                } else {
                    $links .= '<li class="next disabled"><a href="#">&rarr;</a></li>';
                }
            }
            $links .= '</ul>';
            $links .= '</nav>';

            // Return all links of Pagination
            return $links;
        } else {
            return false;
        }
    }
}
