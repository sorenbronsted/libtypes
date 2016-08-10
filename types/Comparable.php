<?php
namespace ufds;

interface Comparable {
	public function isEqual(Comparable $other);
}