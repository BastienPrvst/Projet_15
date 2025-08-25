<?php

namespace App\Tests\Unit;

use App\Entity\Media;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validation;

class MediaTest extends TestCase
{
	#[DataProvider('provideDataUpload')]
	public function testUploadFile(array $data): void
	{
		$validator = Validation::createValidatorBuilder()
			->enableAttributeMapping()
			->getValidator();

		$file = new UploadedFile(
			$data['path'],
			$data['name'],
			$data['mimeType'],
			UPLOAD_ERR_OK,
			true
		);

		$media = new Media();
		$media->setFile($file);

		$violations = $validator->validate($media);

		$this->assertCount($data['expectedErrors'], $violations);
	}

	public static function provideDataUpload(): iterable
	{
		yield 'tooBig' => [[
			'name' => 'home.jpeg',
			'path' => __DIR__ . '/../TestFiles/home.jpeg',
			'mimeType' => 'image/jpeg',
			'expectedErrors' => 1,
		]];

		yield 'badMime' => [[
			'name' => 'test.pdf',
			'path' => __DIR__ . '/../TestFiles/test.pdf',
			'mimeType' => 'application/pdf',
			'expectedErrors' => 1,
		]];

		yield 'good' => [[
			'name' => 'ina.png',
			'path' => __DIR__ . '/../TestFiles/ina.png',
			'mimeType' => 'image/png',
			'expectedErrors' => 0,
		]];
	}

}
