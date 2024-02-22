<?php

namespace RobThree\HumanoID\Test;

use RobThree\HumanoID\HumanoID;
use RobThree\HumanoID\HumanoIDs;
use RobThree\HumanoID\Obfuscatories\SymmetricObfuscatorInterface;
use RobThree\HumanoID\WordFormatOption;
use Spatie\Snapshots\MatchesSnapshots;

class JsonLoadingBugsTest extends BaseTestCase
{
    use MatchesSnapshots;

    public static function jsonShuffleSpaceHumanoId(): HumanoID {
        $jsonData = json_decode(
            file_get_contents(__DIR__ . '/../data/space-words.json'),
            true
        );
        // Always randomize the order each time this is called...
        uksort(
            $jsonData,
            static function ($a, $b) {
                return rand(-1, 1);
            }
        );
        // NOTE: We are sorting to simulate "interop scenarios" - meaning when the JSON file ord data isn't provided by PHP.
        // Crucial step that we can do to gracefully fix this DX issue.

        return new HumanoID(
            $jsonData,
            null,
            '-',
            null,
            null
        );
    }

    public static function jsonShuffleZooHumanoId(): HumanoID {
        $jsonData = json_decode(
            file_get_contents(__DIR__ . '/../data/zoo-words.json'),
            true
        );
        // Always randomize the order each time this is called...
        uksort(
            $jsonData,
            static function ($a, $b) {
                return rand(-1, 1);
            }
        );
        // NOTE: We are sorting to simulate "interop scenarios" - meaning when the JSON file ord data isn't provided by PHP.
        // Crucial step that we can do to gracefully fix this DX issue.

        return new HumanoID(
            $jsonData,
            null,
            '-',
            null,
            null
        );
    }

    public function testSpaceJsonRand() {
        $shuffleHumanoID = self::jsonShuffleSpaceHumanoId();
        $firstTwoDozenIds = [];
        for ($i = 0; $i <= 12; $i++) {
            $firstTwoDozenIds[] = $shuffleHumanoID->create($i + 1024);
        }

        $this->assertMatchesJsonSnapshot($firstTwoDozenIds);
    }


    public function testZooJsonRand() {
        $regularZoo = HumanoIDs::zooIdGenerator();
        $firstTwoDozenIds = [];
        for ($i = 0; $i <= 12; $i++) {
            $firstTwoDozenIds[] = $regularZoo->create($i + 1024);
        }
        $this->assertMatchesJsonSnapshot($firstTwoDozenIds);

        $shuffleHumanoID = self::jsonShuffleZooHumanoId();
        $firstTwoDozenIds = [];
        for ($i = 0; $i <= 12; $i++) {
            $firstTwoDozenIds[] = $shuffleHumanoID->create($i + 1024);
        }

        $this->assertMatchesJsonSnapshot($firstTwoDozenIds);
    }
}