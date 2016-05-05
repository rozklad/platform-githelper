<?php namespace Sanatorium\Githelper\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Sanatorium\Githelper\Repositories\Githelper\GithelperRepositoryInterface;
use Cache;

class GithelpersController extends AdminController
{

    /**
     * Display a listing of githelper.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Output repos
        $repos = [];

        // Paths to find git repositories (without trailing slash)
        $paths = config('sanatorium-githelper.paths');

        foreach ( $paths as $path )
        {
            $dirs = array_filter(glob($path . '/*'), 'is_dir');

            foreach ( $dirs as $dir )
            {

                $is_repo = false;

                if ( file_exists($dir . '/.git') )
                    $is_repo = true;

                if ( $is_repo )
                {

                    $repo = $this->getRepoInformation($dir);

                    $repos[ $dir ] = $repo;

                }

            }

        }

        ksort($repos);

        return view('sanatorium/githelper::index', compact('repos'));
    }

    /**
     * Increase version in tag (f.e. 0.1.0 -> 0.1.1)
     * and push to remote origin master
     * used: MAJOR.MINOR.PATCH (1.0.0)
     *
     * @return mixed
     */
    public function tagpush($type = 'patch')
    {
        $dir = request()->get('dir');

        $repo = $this->getRepoInformation($dir);

        $version_format = '%d.%d.%d';

        list($major, $minor, $patch) = explode('.', $repo['last_tag']);
        
        if ( $patch < 9 && $type == 'patch' )
        {
            $patch ++;
            $new_tag = sprintf($version_format, $major, $minor, $patch);
        } else
        {
            if ( $minor < 9 && ($type == 'minor' || $type == 'patch') )
            {
                $patch = 0;
                $minor ++;
                $new_tag = sprintf($version_format, $major, $minor, $patch);
            } else
            {
                $patch = 0;
                $minor = 0;
                $major ++;
                $new_tag = sprintf($version_format, $major, $minor, $patch);
            }

        }

        exec('git -C "' . $dir . '" add --all');
        exec('git -C "' . $dir . '" commit -a -m "automatic commit"');
        exec('git -C "' . $dir . '" tag ' . $new_tag);
        exec('git -C "' . $dir . '" push -u origin master --tags');

        $this->refreshRepoInformation($dir);

        // @todo - catch exceptions
        $this->alerts->success(trans('sanatorium/githelper::common.messages.tagpush.success', ['tag' => $new_tag]));

        return redirect()->back();
    }

    /**
     * @param      $dir   Target folder to get information from
     * @param bool $cache Take information from cache? (use false to force up to date)
     * @return array [dir => target directory, basename => directory basename, changed_files => number of git tracked
     *               changed files, last_tag => last git tag, has_readme => bool (true = has README.md file)]
     */
    public function getRepoInformation($dir, $cache = true)
    {
        if ( $cache )
        {
            $repo = Cache::rememberForever('sanatorium.githelper.' . $dir, function () use ($dir)
            {
                return $this->getRepoInformation($dir, false);
            });

            $repo['changed_files'] = $this->getChangedFilesCountFromDir($dir);

            return $repo;
        }

        $basename = basename($dir);
        $changed_files = $this->getChangedFilesCountFromDir($dir);
        $last_tag = $this->getLastTagFromDir($dir);
        $has_readme = file_exists($this->getReadmePath($dir));
        $composer = $this->getComposerInfo($dir);

        $repo = [
            'dir'           => $dir,
            'basename'      => $basename,
            'changed_files' => $changed_files,
            'last_tag'      => $last_tag,
            'has_readme'    => $has_readme,
            'type'          => (isset($composer['type']) ? $composer['type'] : 'unknown'),
            'name'          => (isset($composer['name']) ? $composer['name'] : 'unknown'),
            'authors'       => (isset($composer['authors']) ? $composer['authors'] : 'unknown'),
            'langs'         => [
                'en' => file_exists($this->getLangPath($dir, 'en')),
                'cs' => file_exists($this->getLangPath($dir, 'cs')),
            ],
        ];

        return $repo;
    }

    public function getChangedFilesCountFromDir($dir)
    {
        return (int) exec('git -C "' . $dir . '" status | grep \'modified:\' | wc -l');
    }

    public function getLastTagFromDir($dir)
    {
        return exec('git -C "' . $dir . '" describe --tags');
    }

    public function refreshRepoInformation($dir)
    {
        Cache::forget('sanatorium.githelper.' . $dir);

        $this->getRepoInformation($dir);
    }

    public function getReadmePath($dir = null)
    {
        $readmeFilename = 'README.md';

        return $dir . '/' . $readmeFilename;
    }

    public function getLangPath($dir = null, $lang = null)
    {
        $langFolder = 'lang/' . $lang;

        return $dir . '/' . $langFolder;
    }

    /**
     * Create readme file if does not exist
     */
    public function readme()
    {
        $dir = request()->get('dir');

        $readmePath = $this->getReadmePath($dir);

        if ( file_exists($readmePath) )
        {
            $this->alerts->error(trans('sanatorium/githelper::common.messages.readme.exists'));

            return redirect()->back();
        }

        $readmeContents = $this->getReadmeContents($dir);

        file_put_contents($readmePath, $readmeContents);

        $this->refreshRepoInformation($dir);

        $this->alerts->success(trans('sanatorium/githelper::common.messages.readme.success'));

        return redirect()->back();
    }

    public function getComposerInfo($dir = null)
    {
        $composerJsonPath = $dir . '/composer.json';

        if ( file_exists($composerJsonPath) )
        {
            $info = json_decode(file_get_contents($composerJsonPath), true);
        }

        return $info;
    }

    public function getReadmeContents($dir = null)
    {
        $info = $this->getComposerInfo($dir);

        return
            "# " . $info['name'] . "

" . $info['description'] . "

## Documentation

No documentation available.

## Changelog

Changelog not available.

## Support

Support not available.";

    }

}
