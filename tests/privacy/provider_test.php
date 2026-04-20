<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tests for the format_trail privacy provider.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_trail\tests\privacy;

use core_privacy\tests\provider_testcase;
use format_trail\privacy\provider;

/**
 * Tests for format_trail\privacy\provider.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \format_trail\privacy\provider
 */
class provider_test extends provider_testcase {
    /**
     * Test that the plugin correctly declares it stores no personal data.
     *
     * @covers \format_trail\privacy\provider::get_reason
     */
    public function test_get_reason_returns_nop(): void {
        $this->assertSame('privacy:nop', provider::get_reason());
    }

    /**
     * Test that the provider implements the null_provider interface.
     *
     * @covers \format_trail\privacy\provider
     */
    public function test_implements_null_provider(): void {
        $this->assertInstanceOf(
            \core_privacy\local\metadata\null_provider::class,
            new provider()
        );
    }
}
