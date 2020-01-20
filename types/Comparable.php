<?php
namespace sbronsted;

interface Comparable {
	public function isEqual(Comparable $other);
}