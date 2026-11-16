<?php
use Degriz\Euguarantee\Language;
use PHPUnit\Framework\TestCase;

class Language_Test extends TestCase {

	public function test_locale_maps_to_a_notice_language(): void {
		$this->assertSame( 'sl', Language::for_locale( 'sl_SI' ) );
		$this->assertSame( 'de', Language::for_locale( 'de_DE_formal' ) );
		$this->assertSame( 'hr', Language::for_locale( 'hr' ) );
		$this->assertSame( 'ga', Language::for_locale( 'ga_IE' ) );
		$this->assertSame( 'en', Language::for_locale( 'en_US' ) );
	}

	public function test_unpublished_languages_fall_back_to_english(): void {
		$this->assertSame( 'en', Language::for_locale( 'sr_RS' ) );
		$this->assertSame( 'en', Language::for_locale( 'ja' ) );
		$this->assertSame( 'en', Language::for_locale( '' ) );
		$this->assertSame( 'en', Language::for_locale( '../../x' ), 'a locale is never used as a path' );
	}

	public function test_all_24_languages_are_listed(): void {
		$this->assertCount( 24, Language::AVAILABLE );
		$this->assertCount( 24, array_unique( Language::AVAILABLE ) );
	}

	public function test_info_url(): void {
		$this->assertSame( 'https://europa.eu/youreurope/citizens/consumers/shopping/guarantees/index_sl.htm', Language::info_url( 'sl' ) );
		$this->assertSame( 'https://europa.eu/youreurope/citizens/consumers/shopping/guarantees/index_en.htm', Language::info_url( 'xx' ) );
	}
}
