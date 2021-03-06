<?php

namespace PHPSemVerChecker\Test\Analyzer;

use PhpParser\Node\Stmt\Trait_;
use PHPSemVerChecker\Analyzer\TraitAnalyzer;
use PHPSemVerChecker\Registry\Registry;
use PHPSemVerChecker\SemanticVersioning\Level;
use PHPSemVerChecker\Test\Assertion\Assert;
use PHPSemVerChecker\Test\TestCase;

class TraitAnalyzerTest extends TestCase {
	public function testCompareSimilarTrait()
	{
		$before = new Registry();
		$after = new Registry();

		$traitBefore = new Trait_('tmp');
		$before->addTrait($traitBefore);

		$traitAfter = new Trait_('tmp');
		$after->addTrait($traitAfter);

		$analyzer = new TraitAnalyzer();
		$report = $analyzer->analyze($before, $after);

		Assert::assertNoDifference($report);
	}

	public function testTraitRemoved()
	{
		$before = new Registry();
		$after = new Registry();

		$before->addTrait(new Trait_('tmp'));

		$analyzer = new TraitAnalyzer();
		$report = $analyzer->analyze($before, $after);

		$context = 'trait';
		$expectedLevel = Level::MAJOR;
		Assert::assertDifference($report, $context, $expectedLevel);
		$this->assertSame('V037', $report[$context][$expectedLevel][0]->getCode());
		$this->assertSame('Trait was removed.', $report[$context][$expectedLevel][0]->getReason());
		$this->assertSame('tmp', $report[$context][$expectedLevel][0]->getTarget());
	}

	public function testTraitAdded()
	{
		$before = new Registry();
		$after = new Registry();

		$after->addTrait(new Trait_('tmp'));

		$analyzer = new TraitAnalyzer();
		$report = $analyzer->analyze($before, $after);

		$context = 'trait';
		$expectedLevel = Level::MINOR;
		Assert::assertDifference($report, $context, $expectedLevel);
		$this->assertSame('V046', $report[$context][$expectedLevel][0]->getCode());
		$this->assertSame('Trait was added.', $report[$context][$expectedLevel][0]->getReason());
		$this->assertSame('tmp', $report[$context][$expectedLevel][0]->getTarget());
	}
}
