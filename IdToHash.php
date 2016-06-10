<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 10/06/2016
 * Time: 00:43
 */

class IdToHash {
	protected $alphabet;
	protected $block_size;
	protected $mask;
	protected $mapping;

	/**
	 * UrlHash constructor.
	 *
	 * @param $data array
	 * @param $data['alphabet'] string
	 * @param $data['nullAlphabet'] string
	 * @param $data['block_size'] integer
	 */
	public function __construct($data) {
		$this->alphabet     = $data['alphabet'];
		$this->nullAlphabet = $data['nullAlphabet'];
		$this->blockSize    = $data['block_size'];
		$this->mask         = (1 << $this->blockSize) - 1;
		$this->mapping      = array_reverse(range(0, $this->blockSize-1));
	}


	public function encode($number, $minLen=0){
		/*
		    def encode_url(self, n, min_length=MIN_LENGTH):
		        return self.enbase(self.encode(n), min_length)
		 */
		return $this->toBase($this->encodeNumber($number), $minLen);
	}

	public function decode($string){
		/*
		    def decode_url(self, n):
		        return self.decode(self.debase(n))
		 */
		return $this->decodeNumber($this->fromBase($string));
	}


	protected function encodeNumber($number){
		/*
		    def encode(self, n):
		        return (n & ~self.mask) | self._encode(n & self.mask)
		 */
		return ($number & ~$this->mask) | $this->shiftNumberBits($number & $this->mask);
	}

	protected function shiftNumberBits($number){
		/*
		    def _encode(self, n):
		        result = 0
		        for i, b in enumerate(reversed(self.mapping)):
		            if n & (1 << i):
		                result |= (1 << b)
		        return result
		 */
		$r = 0;
		foreach ($this->mapping as $i=>$b){
			if ($number & (1 << $i))
				$r |= (1 << $b);
		}

		return $r;
	}


	protected function decodeNumber($number){
		/*
		    def decode(self, n):
		        return (n & ~self.mask) | self._decode(n & self.mask)
		 */

		return ($number & ~$this->mask) | $this->unshiftNumberBits($number & $this->mask);
	}

	protected function unshiftNumberBits($number){
		/*
		    def _decode(self, n):
		        result = 0
		        for i, b in enumerate(reversed(self.mapping)):
		            if n & (1 << b):
		                result |= (1 << i)
		        return result
		 */

		$r = 0;
		foreach ($this->mapping as $i=>$b){
			if ($number & (1 << $b))
				$r |= (1 << $i);
		}

		return $r;
	}

	protected function toBase($number, $minLen){
		/*
		    def enbase(self, x, min_length=MIN_LENGTH):
		        result = self._enbase(x)
		        padding = self.alphabet[0] * (min_length - len(result))
		        return '%s%s' % (padding, result)
		 */

		$r = $this->numberToBase($number);

		$paddingSize = $minLen - strlen($r);
		$padding = '';
		$nullAlphabetSize = strlen($this->nullAlphabet);
		$nullAlphabet = str_split($this->nullAlphabet);

		for ($i = 0; $i < $paddingSize; $i++){
			$padding .= $nullAlphabet[ ($i + $number) % $nullAlphabetSize];
		}

		return $padding.$r;
	}

	protected function numberToBase($number){
		/*
		    def _enbase(self, x):
		        n = len(self.alphabet)
		        if x < n:
		            return self.alphabet[x]
		        return self._enbase(int(x // n)) + self.alphabet[int(x % n)]
		 */

		$alphabetSize = strlen($this->alphabet);
		$alphabet = str_split($this->alphabet);

		if ($number < $alphabetSize){
			return $alphabet[$number];
		}else{
			return $this->numberToBase(floor($number / $alphabetSize)) . $alphabet[$number % $alphabetSize];
		}
	}

	protected function fromBase($string){
		/*
		    def debase(self, x):
		        n = len(self.alphabet)
		        result = 0
		        for i, c in enumerate(reversed(x)):
		            result += self.alphabet.index(c) * (n ** i)
		        return result
		 */
		$alphabetSize = strlen($this->alphabet);
		$strLength = strlen($string);
		$reversedString = strrev($string);

		$r = 0;
		for ($i = 0; $i < $strLength; $i++){
			$c = $reversedString[$i];
			$idx = strpos($this->alphabet, $c);

			if ($idx !== false){
				$r += $idx * pow($alphabetSize, $i);
			}
		}

		return $r;
	}

	public function Test(){
		echo "=> Testing UrlHash class:\n\n";

		function toBin($n){
			return str_pad(decbin($n), 64, '0', STR_PAD_LEFT);
		}

		echo "==>  Mask:             ".toBin($this->mask)." \n";
		echo "==> ~Mask:             ".toBin(~$this->mask)." \n";


		for ($i = 0; $i < 15; $i++){
			echo "\n";

			$number = rand(0, (1 << $this->blockSize-1));

			echo "= Testing with number: ".toBin($number)." | $number  \n";
			echo "\n";

			$nmask = $number & $this->mask;
			echo "Number & Mask:         ".toBin($nmask)." | $nmask \n";

			$nmask = $number & ~$this->mask;
			echo "Number & ~Mask:        ".toBin($nmask)." | $nmask \n";

			echo "\n";

			$shiftedNumber = $this->shiftNumberBits($number);
			echo "Shifted number:        ".toBin($shiftedNumber)." | $shiftedNumber \n";

			$unshiftedNumber = $this->unshiftNumberBits($shiftedNumber);
			echo "Unshifted number:      ".toBin($unshiftedNumber)." | $unshiftedNumber \n";

			echo "\n";


			$encodedNumber = $this->encodeNumber($number);
			echo "Encoded number:        ".toBin($encodedNumber)." | $encodedNumber \n";

			echo "\n";

			$nmask = $encodedNumber & $this->mask;
			echo "Number & Mask:         ".toBin($nmask)." | $nmask \n";

			$nmask = $encodedNumber & ~$this->mask;
			echo "Encoded & ~Mask:       ".toBin($nmask)." | $nmask \n";

			echo "\n";

			$decodedNumber = $this->decodeNumber($encodedNumber);
			echo "Decoded number:        ".toBin($decodedNumber)." | $decodedNumber \n";

			$result = $number == $decodedNumber ? 'PASS!' : 'FAIL!';
			echo "Number encode/decode:  $result \n";

			echo "\n";

			$encoded = $this->encode($number);
			echo "Encoded hash:          $encoded \n";

			$decoded = $this->decode($encoded);
			echo "Decoded hash:          $decoded \n";

			$result = $number == $decoded ? 'PASS!' : 'FAIL!';
			echo "Hash encode/decode:    $result \n";

			echo "\n";

			$encoded = $this->encode($number, 10);
			echo "Encoded hash:          $encoded \n";

			$decoded = $this->decode($encoded);
			echo "Decoded hash:          $decoded \n";

			$result = $number == $decoded ? 'PASS!' : 'FAIL!';
			echo "Padding encode/decode: $result \n";
		}

	}
}

