<?php

namespace Bookshop;

/**
 * PagingResult
 *
 * Wraps a paginated result set with offset and total count.
 * Uses PHP 8 constructor property promotion.
 */
class PagingResult {
  /**
   * @param array $result      the current page of results
   * @param int   $offset      zero-based start index of this page
   * @param int   $totalCount  total number of matching items
   */
  public function __construct(
    private array $result,
    private int $offset,
    private int $totalCount,
  ) {}

  /**
   * @return array current page of results
   */
  public function getResult(): array {
    return $this->result;
  }

  /**
   * @return int zero-based start index of this page
   */
  public function getOffset(): int {
    return $this->offset;
  }

  /**
   * @return int total number of matching items
   */
  public function getTotalCount(): int {
    return $this->totalCount;
  }

  /**
   * @return int 1-based position of the first item on this page
   */
  public function getPositionOfFirst(): int {
    return $this->getOffset() + 1;
  }

  /**
   * @return int 1-based position of the last item on this page
   */
  public function getPositionOfLast(): int {
    return $this->getOffset() + sizeof($this->result);
  }
}
