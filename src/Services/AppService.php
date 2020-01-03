<?php


namespace App\Services;


use App\Entity\Character;
use App\Entity\Music;
use App\Entity\NoteType;
use App\Entity\Scene;
use App\Entity\SceneElement;
use App\Entity\Script;
use App\Entity\Work;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Screenplay\Extractor;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Yaml\Yaml;

// consider this format: https://www.icomedytv.com/comedy-scripts/funny/humorous/comedy-monologues/cell-phones
// and making this an interface, with different implementation, ScriptImportInterface (FountainImportService, etc.)

class AppService
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $serializer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    private $lines = [];


    private function push($string, $addBlank=false) {
        $string = trim($string);
        array_push($this->lines, $string);
        if ($addBlank) {
            array_push($this->lines, '');
        }
    }
    public function workToFountain(Work $work): string
    {
        // @todo: title, copyright, etc.
        foreach ($work->getChapters() as $chapter) {
            $this->push('.' . $chapter->getDescription(), true);
            foreach ($chapter->getParagraphs() as $paragraph) {
                $this->push(strtoupper($paragraph->getCharId()));

                // if [p], assume lyrics
                $stanza = explode('[p]', $paragraph->getPlainText());
                if (count($stanza) > 1) {
                    foreach ($stanza as $line) {
                        $this->push('~' . $line);
                    }
                    // blank at end
                    $this->push('');
                } else {
                    $this->push($paragraph->getPlainText(), true);
                }
            }
        }

        $text = join("\n", $this->lines);

        return $text;
    }

    function extractBz2( $zipFile = '', $dirFromZip = '' )
    {

        define(DIRECTORY_SEPARATOR, '/');

        $zipDir = getcwd() . DIRECTORY_SEPARATOR;
        $fn = $zipDir . $zipFile;
        $phar = new \PharData($fn);

        foreach (new \RecursiveIteratorIterator($phar) as $file) {
            // $file is a PharFileInfo class, and inherits from SplFileInfo
            echo $file->getFileName() . "\n";
            echo file_get_contents($file->getPathName()) . "\n"; // display contents;
            dd($file);
        }

        foreach ($phar as $p) {
            dd($p);
        }
        $content = $phar->count();
        dd($content);
    }

    function extractZip( $zipFile = '', $dirFromZip = '' )
{

    define(DIRECTORY_SEPARATOR, '/');

    $zipDir = getcwd() . DIRECTORY_SEPARATOR;
    $zip = zip_open($zipDir.$zipFile);

    if ($zip)
    {
        while ($zip_entry = zip_read($zip))
        {
            $completePath = $zipDir . dirname(zip_entry_name($zip_entry));
            $completeName = $zipDir . zip_entry_name($zip_entry);


            // Walk through path to create non existing directories
            // This won't apply to empty directories ! They are created further below
            if(!file_exists($completePath) && preg_match( '#^' . $dirFromZip .'.*#', dirname(zip_entry_name($zip_entry)) ) )
            {
                $tmp = '';
                foreach(explode('/',$completePath) AS $k)
                {
                    $tmp .= $k.'/';
                    if(!file_exists($tmp) )
                    {
                        dd($zip_entry, $completeName, $completePath, $tmp);
                        @mkdir($tmp, 0777);
                    }
                }
            }

            if (zip_entry_open($zip, $zip_entry, "r"))
            {
                if( preg_match( '#^' . $dirFromZip .'.*#', dirname(zip_entry_name($zip_entry)) ) )
                {
                    if ($fd = @fopen($completeName, 'w+'))
                    {
                        fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
                        fclose($fd);
                    }
                    else
                    {
                        // We think this was an empty directory
                        mkdir($completeName, 0777);
                    }
                    zip_entry_close($zip_entry);
                }
            }
        }
        zip_close($zip);
    }
    return true;
}




}