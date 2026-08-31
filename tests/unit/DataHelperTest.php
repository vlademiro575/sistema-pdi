<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

final class DataHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('data');
    }

    public function testFormatarDataValida(): void
    {
        $this->assertSame('31/08/2026', formatar_data('2026-08-31'));
        $this->assertSame('15/05/2025', formatar_data('2025-05-15 14:30:00'));
    }

    public function testFormatarDataVaziaOuInvalida(): void
    {
        $this->assertSame('-', formatar_data(null));
        $this->assertSame('-', formatar_data(''));
        $this->assertSame('-', formatar_data('0000-00-00'));
        $this->assertSame('N/A', formatar_data(null, 'N/A'));
        $this->assertSame('-', formatar_data('data-invalida'));
    }

    public function testFormatarDataHora(): void
    {
        $this->assertSame('31/08/2026 às 14:30:00', formatar_data_hora('2026-08-31 14:30:00'));
        $this->assertSame('31/08/2026 14:30', formatar_data_hora('2026-08-31 14:30:00', false));
        $this->assertSame('-', formatar_data_hora(null));
    }

    public function testConverterDataIso(): void
    {
        $this->assertSame('2026-08-31', converter_data_iso('31/08/2026'));
        $this->assertNull(converter_data_iso(null));
        $this->assertSame('2026-08-31', converter_data_iso('2026-08-31'));
    }
}

