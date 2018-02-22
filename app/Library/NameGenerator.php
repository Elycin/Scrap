<?php
/**
 * Created by PhpStorm.
 * User: elyci
 * Date: 2/18/2018
 * Time: 12:14 AM
 */

namespace App\Library;


use App\Upload;

class NameGenerator
{
    public const DICTIONARY = [
        "animals"    => [
            "Aardvark", "Albatross", "Alligator", "Alpaca",
            "Ant", "Anteater", "Antelope", "Ape",
            "Armadillo", "Ayeaye", "Babirusa", "Baboon",
            "Badger", "Barracuda", "Bat", "Bear",
            "Beaver", "Bee", "Bison", "Boar",
            "Buffalo", "Butterfly", "Camel", "Caribou",
            "Cat", "Caterpillar", "Cattle", "Chamois",
            "Cheetah", "Chicken", "Chimpanzee", "Chinchilla",
            "Chough", "Clam", "Cobra", "Cockroach",
            "Cod", "Corgi", "Cormorant", "Coyote",
            "Crab", "Crane", "Crocodile", "Crow",
            "Curlew", "Deer", "Dinosaur", "Dog",
            "Dogfish", "Dolphin", "Donkey", "Dotterel",
            "Doge", "Dove", "Dragon", "Dragonfly",
            "Duck", "Dugong", "Dunlin", "Eagle",
            "Echidna", "Eel", "Eland", "Elephant",
            "Elk", "Emu", "Falcon", "Ferret", "Finch",
            "Fish", "Flamingo", "Fly", "Fossa",
            "Fowl", "Fox", "Frog", "Galago",
            "Gaur", "Gazelle", "Gerbil", "Gerenuk",
            "Giant", "Giraffe", "Gnat", "Gnu",
            "Goat", "Goldfinch", "Goldfish", "Goose",
            "Gorilla", "Goshawk", "Grasshopper", "Grouse",
            "Guanaco", "Guinea", "Gull", "Hamster", "Hare",
            "Hawk", "Hedgehog", "Heron", "Herring", "Hippopotamus",
            "Hornet", "Horse", "Human", "Hummingbird",
            "Hyena", "Jackal", "Jaguar", "Jay",
            "Jellyfish", "Joey", "Kangaroo", "Kiwi",
            "Koala", "Komodoa", "Kouprey", "Kudu",
            "Lamprey", "Lapwing", "Lark", "Lemur",
            "Leopard", "Lion", "Llama", "Lobster",
            "Locust", "Loris", "Louse", "Lyrebird",
            "Magpie", "Mallard", "Manatee", "Marten",
            "Meerkat", "Mink", "Mole", "Monkey",
            "Moose", "Mosquito", "Mouse", "Mule",
            "Narwhal", "Newt", "Nightingale", "Octopus",
            "Okapi", "Opossum", "Oryx", "Ostrich",
            "Otter", "Owl", "Ox", "Oyster",
            "Panda", "Panther", "Parrot", "Partridge",
            "Peafowl", "Pelican", "Penguin", "Pheasant",
            "pig", "Pig", "Pigeon", "Pony", "Porcupine",
            "Porpoise", "Prairie", "Quail", "Quelea",
            "Rabbit", "Raccoon", "Rail", "Ram", "Rat",
            "Raven", "Red", "Reindeer", "Rhinoceros",
            "Rook", "Ruff", "Salamander", "Salmon",
            "Samoyed", "Sandpiper", "Sardine", "Scorpion",
            "Seahorse", "Seal", "Sealion", "Seaurchin",
            "Shark", "Sheep", "Shrew", "Shrimp", "Skunk",
            "Snail", "Snake", "Spider", "Squid", "Squirrel",
            "Starling", "Stingray", "Stinkbug", "Stork",
            "Swallow", "Swan", "Tapir", "Tarsier", "Termite",
            "Tiger", "Toad", "Trout", "Turkey", "Turtle",
            "Viper", "Vulture", "Wallaby", "Walrus", "Wasp",
            "Water", "Weasel", "Whale", "Wolf", "Wolverine",
            "Wombat", "Woodcock", "Woodpecker", "Worm", "Wren",
            "Yak", "Zebra"
        ],
        "colors"     => [
            "Black", "Blue", "Blue", "Brown",
            "Cerulean", "Cyan", "Green", "Green",
            "Lime", "Magenta", "Maroon", "Orange",
            "Purple", "Red", "Silver", "Veridian",
            "Violet", "White", "Yellow"
        ],
        "adjectives" => [
            "Absurd", "Amazing", "Amusing", "Angry",
            "Arrogant", "Ashamed", "Astonishing", "Astounding",
            "Awful", "Beatific", "Bewildering", "Breathtaking",
            "Broad", "Bucolic", "Cheerful", "Chucklesome",
            "Confused", "Creepy", "Disconcerting", "Droll",
            "Dulcet", "Dusty", "Enormous", "Evil", "Fierce",
            "Fluffy", "Foolish", "Frightened", "Gentle",
            "Gigantic", "Gratuitous", "Hilarious", "Huge",
            "Humorous", "Hysterical", "Idyllic", "Jolly",
            "Kind", "Laughable", "Mellifluous", "Meretricious",
            "Miniature", "Numinous", "Proud", "Quaint",
            "Ridiculous", "Shallow", "Shocking", "Silent",
            "Silly", "Soft", "Startling", "Stunning",
            "Ubiquitous", "Witty"
        ],
    ];

    public const VALID_CHARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';

    /**
     * Random String Type Generation
     *
     * Generate a random filename.
     *
     * @param null $extension
     * @param int  $min_override
     * @param int  $max_override
     * @return string
     */
    public static function randomTypeGeneration($extension = null, int $min_override, int $max_override): string
    {
        // determine lengths.
        $min_decision = intval(config('app.minimum_file_basename_length', $min_override));
        $max_decision = intval(config('app.maximum_file_basename_length', $max_override));

        while (true) {
            // Placeholder value, will be appended to.
            $random_string = '';

            // Generate random string based on the length
            for ($i = 0; $i < mt_rand($min_decision, $max_decision); $i++) $random_string .= self::VALID_CHARS[mt_rand(0, strlen(self::VALID_CHARS) - 1)];

            // Assign the compiled variable
            $compiled = (empty($extension)) ? $random_string : sprintf("%s.%s", $random_string, $extension);

            // Check if it doesn't exist.
            if (!self::exists($compiled)) return $compiled;
        }
    }

    /**
     * Name Type Filename Generator
     *
     * Generate a random filename based on the dictionary.
     *
     * @param null $extension
     * @return string
     */
    public static function nameTypeGeneration($extension = null): string
    {
        while (true) {
            // Generate the base name.
            $base_generation = sprintf("%s%s%s%s",
                array_random(self::DICTIONARY["adjectives"]),
                array_random(self::DICTIONARY["colors"]),
                array_random(self::DICTIONARY["animals"]),
                mt_rand(0, 9)
            );

            // Assign the compiled variable
            $compiled = (empty($extension)) ? $base_generation : sprintf("%s.%s", $base_generation, $extension);

            // Check if it doesn't exist.
            if (!self::exists($compiled)) return $compiled;
        }
    }

    public static function exists(string $gen)
    {
        return Upload::where('alias', $gen)->first();
    }
}
