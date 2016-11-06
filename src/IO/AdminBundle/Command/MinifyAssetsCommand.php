<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MinifyAssetsCommand
 * @package AdminBundle\Command
 */
class MinifyAssetsCommand extends ContainerAwareCommand  {

	/**
	 * @const array ASSET_LIST
	 */
	const ASSET_LIST = array(
		// [type] [uglify] [combine file name|no]
		// [source file name] [destination file name]
		// [source path] [destination path]

		['css', 'no', 'no',
			'bootstrap.css',
			'bootstrap.%s.css',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'bootstrap.css.map',
			'bootstrap.css.map',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'bootstrap.min.css',
			'bootstrap.%s.min.css',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'bootstrap.min.css.map',
			'bootstrap.min.css.map',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'bootstrap-theme.css',
			'bootstrap-theme.%s.css',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'bootstrap-theme.css.map',
			'bootstrap-theme.css.map',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'style.%s.min.css',
			'bootstrap-theme.min.css',
			'bootstrap-theme.%s.min.css',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'bootstrap-theme.min.css.map',
			'bootstrap-theme.min.css.map',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'main.css',
			'main.%s.css',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'main.css.map',
			'main.css.map',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'style.%s.min.css',
			'main.min.css',
			'main.%s.min.css',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['css', 'no', 'no',
			'main.min.css.map',
			'main.min.css.map',
			'theme_source/bootstrap-3.3.7/dist/css',
			'src/AppBundle/Resources/public/css'],
		['js', 'no', 'no',
			'bootstrap.js',
			'bootstrap.%s.js',
			'theme_source/bootstrap-3.3.7/dist/js',
			'src/AppBundle/Resources/public/js/lib'],
		['js', 'no', 'no',
			'bootstrap.min.js',
			'bootstrap.%s.min.js',
			'theme_source/bootstrap-3.3.7/dist/js',
			'src/AppBundle/Resources/public/js/lib'],
		['js', 'yes', 'graphics.%s.min.js',
			'dashboardGraphic.js',
			'dashboardGraphic.%s.min.js',
			'src/AppBundle/Resources/public/js/development',
			'src/AppBundle/Resources/public/js/prod'],
		['js', 'yes', 'common.%s.min.js',
			'IORoute.js',
			'IORoute.%s.min.js',
			'src/AppBundle/Resources/public/js/development',
			'src/AppBundle/Resources/public/js/prod'],
		['js', 'yes', 'common.%s.min.js',
			'user.js',
			'user.%s.min.js',
			'src/AppBundle/Resources/public/js/development',
			'src/AppBundle/Resources/public/js/prod'],
		['js', 'yes', 'no',
			'main.js',
			'main.%s.min.js',
			'src/AppBundle/Resources/public/js/development',
			'src/AppBundle/Resources/public/js/prod'],
	);


	/**
	 * @const string JS_TEMPLATES
	 */
	const JS_TEMPLATES = array(
		// [uglify] [combine file name|no]
		// [destination file name]
		// [source path] [destination path]
		['yes', 'master.%s.min.js',
			'masterpage.%s.min.js',
			'AppBundle::masterpage.js.html.twig',
			'src/AppBundle/Resources/public/js/prod']
	);

    /**
     * {@inheritdoc}
     */
    protected function configure() {

	    $this
			->setName('admin:minify_assets')
			->setDescription('Minify css and javascript assets')
			->setHelp("admin:minify_assets_command [asset version]")
			->addArgument('assetVersion', InputArgument::REQUIRED, 'Asset Version')
		;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

		$assetVersion = $input->getArgument('assetVersion');
		/** @var \AppKernel $appKernel */
		$appKernel = $this->getContainer()->get('kernel');
		$rootDir = $appKernel->getRootDir() . '/../';

		// [type] [uglify] [combine file name|no]
		// [source file name] [destination file name]
		// [source path] [destination path]

		$combineFiles = array();
		$currentIdx = 0;

		foreach ( self::ASSET_LIST as $asset ) {

			$destinationPath = $rootDir . $asset[6];
			$sourcePath      = $rootDir . $asset[5];

			$destinationFileName = ( strpos( $asset[4], '%s' ) ) ? vsprintf( $asset[4], $assetVersion ) : $asset[4];
			$destinationFile     = $destinationPath . '/' . $destinationFileName;
			$sourceFile          = $sourcePath . '/' . $asset[3];
			$combineFileGroup    = ( strpos( $asset[2], '%s' ) ) ? vsprintf( $asset[2], $assetVersion ) : $asset[2];
			$uglify              = ( $asset[1] == 'yes' ) ? true : false;

			if ( file_exists( $destinationFile ) ) {

				unlink( $destinationFile );
			}

			if ( $asset[0] == 'css' ) {

				copy( $sourceFile, $destinationFile );
				$this->addFileToCombineList( $combineFiles, $combineFileGroup, $currentIdx, $destinationPath . '/' . $combineFileGroup);
				$output->writeln( 'Copy file completed ' . $destinationFileName );

			} elseif ( $asset[0] == 'js' ) {

				if ( ! $uglify ) {

					copy( $sourceFile, $destinationFile );
					$output->writeln( 'Copy file copleted ' . $destinationFileName );

				} else {

					$execCommand = 'uglifyjs ' . $sourceFile . ' -c -o ' . $destinationFile;
					$outputsUJS  = array();
					exec( $execCommand, $outputsUJS );

					foreach ( $outputsUJS as $outputUJS ) {

						$output->writeln( $outputUJS );
					}

					$output->writeln( 'Uglify file completed ' . $destinationFileName );
				}

				$this->addFileToCombineList( $combineFiles, $combineFileGroup, $currentIdx, $destinationPath . '/' . $combineFileGroup);
			}

			$currentIdx += 1;
		}

	    foreach ( $combineFiles as $fileName => $fileIndexes ) {

		    if ( $fileName == 'no' ) {
			    continue;
		    }

		    $output->writeln( 'Combining file ' . $fileName );

		    foreach ( $fileIndexes as $fileIdx ) {

			    $destinationPath = $rootDir . self::ASSET_LIST[ $fileIdx ][6];
			    $destinationFile = $destinationPath . '/' . $fileName;
			    $sourceFileName  = ( strpos( self::ASSET_LIST[ $fileIdx ][4], '%s' ) ) ? vsprintf( self::ASSET_LIST[ $fileIdx ][4], $assetVersion ) : self::ASSET_LIST[ $fileIdx ][4];
			    $sourceFile      = $destinationPath . '/' . $sourceFileName;

			    if ( file_exists( $destinationFile ) ) {

				    $fileHandle = fopen( $destinationFile, 'a' );
				    fwrite( $fileHandle, "\n" );
				    fwrite( $fileHandle, file_get_contents( $sourceFile ) );
				    fclose( $fileHandle );
			    } else {
				    $fileHandle = fopen( $destinationFile, 'w' );
				    fwrite( $fileHandle, file_get_contents( $sourceFile ) );
				    fclose( $fileHandle );
			    }

			    unlink( $sourceFile );
		    }
	    }

	    $combineFiles = array();
	    $currentIdx = 0;

	    foreach (self::JS_TEMPLATES as $jsTemplate) {

		    $destinationPath = $rootDir . $jsTemplate[4];

		    $destinationFileName = ( strpos( $jsTemplate[2], '%s' ) ) ? vsprintf( $jsTemplate[2], $assetVersion ) : $jsTemplate[2];
		    $destinationFile     = $destinationPath . '/' . $destinationFileName;
		    $combineFileGroup    = ( strpos( $jsTemplate[1], '%s' ) ) ? vsprintf( $jsTemplate[1], $assetVersion ) : $jsTemplate[1];
		    $uglify              = ( $jsTemplate[0] == 'yes' ) ? true : false;

		    if ( file_exists( $destinationFile ) ) {

			    unlink( $destinationFile );
		    }

		    /** @var TwigEngine $templating */
		    $templating = $this->getContainer()->get('templating');
		    $renderedJS = $templating->render($jsTemplate[3], array('compileMod' => true));
		    $tmpDestinationFile = $destinationPath . '/tmp_' . $destinationFileName;
		    if(file_exists($tmpDestinationFile)) {
			    unlink($tmpDestinationFile);
		    }

		    $tmpDestinationFileHandle = fopen($tmpDestinationFile, 'w');
		    fwrite($tmpDestinationFileHandle, $renderedJS);
		    fclose($tmpDestinationFileHandle);

		    if ( ! $uglify ) {

			    copy( $tmpDestinationFile, $destinationFile );
			    $output->writeln( 'Copy file copleted ' . $destinationFileName );
			    unlink($tmpDestinationFile);

		    } else {

			    $execCommand = 'uglifyjs ' . $tmpDestinationFile . ' -c -o ' . $destinationFile;
			    $outputsUJS  = array();
			    exec( $execCommand, $outputsUJS );

			    foreach ( $outputsUJS as $outputUJS ) {

				    $output->writeln( $outputUJS );
			    }

			    $output->writeln( 'Uglify file completed ' . $destinationFileName );
			    unlink($tmpDestinationFile);
		    }

		    $this->addFileToCombineList( $combineFiles, $combineFileGroup, $currentIdx, $destinationPath . '/' . $combineFileGroup );
		    $currentIdx += 1;
	    }

	    foreach ( $combineFiles as $fileName => $fileIndexes ) {

		    if ( $fileName == 'no' ) {
			    continue;
		    }

		    $output->writeln( 'Combining file ' . $fileName );

		    foreach ( $fileIndexes as $fileIdx ) {

			    $destinationPath = $rootDir . self::JS_TEMPLATES[ $fileIdx ][4];
			    $destinationFile = $destinationPath . '/' . $fileName;
			    $sourceFileName = ( strpos( self::JS_TEMPLATES[ $fileIdx ][2], '%s' ) ) ? vsprintf( self::JS_TEMPLATES[ $fileIdx ][2], $assetVersion ) : self::JS_TEMPLATES[ $fileIdx ][2];
			    $sourceFile      = $destinationPath . '/' . $sourceFileName;

			    if ( file_exists( $destinationFile ) ) {

				    $fileHandle = fopen( $destinationFile, 'a' );
				    fwrite( $fileHandle, "\n" );
				    fwrite( $fileHandle, file_get_contents( $sourceFile ) );
				    fclose( $fileHandle );
			    } else {
				    $fileHandle = fopen( $destinationFile, 'w' );
				    fwrite( $fileHandle, file_get_contents( $sourceFile ) );
				    fclose( $fileHandle );
			    }

			    unlink( $sourceFile );
		    }
	    }
    }

	/**
	 * @param array $combineFiles
	 * @param string $group
	 * @param integer $index
	 */
	private function addFileToCombineList(&$combineFiles, $group, $index, $filePath) {

		if(!isset($combineFiles[$group])) {

			$combineFiles[$group] = array();
		}

		if(file_exists($filePath)) {
			unlink($filePath);
		}

		$combineFiles[$group][] = $index;
    }
}
