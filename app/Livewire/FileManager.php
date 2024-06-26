<?php

namespace App\Livewire;

use Livewire\Component;
use phpseclib3\Net\SSH2;

class FileManager extends Component
{
    public $directories = [];
    public $files = [];
    public $currentPath = '/var/www/html/';
    public $expandedFolders = [];
    public $isLoading = false;

    public $showEditorModal = false;
    public $editableFileContent = '';
    public $editingFilePath = '';

    public function mount()
    {
        $this->listDirectories();
        $this->listFiles();
    }

    public function listDirectories()
    {
        $this->isLoading = true;
        $ssh = $this->getSSHConnection();
        $result = $ssh->exec("find {$this->currentPath} -type d");
        $dirs = explode("\n", trim($result));
        $this->directories = $this->buildDirectoryTree($dirs);
        $this->isLoading = false;
    }

    public function toggleDirectory($path)
    {
        $this->isLoading = true;
        if (isset($this->expandedFolders[$path])) {
            unset($this->expandedFolders[$path]);
        } else {
            $this->expandedFolders[$path] = true;
        }
        $this->isLoading = false;
    }

    public function openDirectory($path)
    {
        $this->isLoading = true;
        $this->currentPath = rtrim($path, '/') . '/';
        $this->listFiles();
        $this->isLoading = false;
    }

    public function listFiles()
    {
        $ssh = $this->getSSHConnection();
        $result = $ssh->exec('ls -la ' . escapeshellarg($this->currentPath));
        $lines = explode("\n", trim($result));
        array_shift($lines); // Remove the total line

        $this->files = array_filter(
            array_map(function ($line) {
                $parts = preg_split('/\s+/', $line, 9);
                if (count($parts) >= 9 && $parts[8] !== '.' && $parts[8] !== '..') {
                    return [
                        'permissions' => $parts[0],
                        'name' => $parts[8],
                        'size' => $parts[4],
                        'modified' => $parts[5] . ' ' . $parts[6] . ' ' . $parts[7],
                        'isDirectory' => $parts[0][0] === 'd',
                    ];
                }
            }, $lines),
        );

        usort($this->files, function ($a, $b) {
            if ($a['isDirectory'] && !$b['isDirectory']) {
                return -1;
            } elseif (!$a['isDirectory'] && $b['isDirectory']) {
                return 1;
            } else {
                return strcmp($a['name'], $b['name']);
            }
        });
    }

    private function buildDirectoryTree($dirs)
    {
        $tree = [];
        foreach ($dirs as $dir) {
            if ($dir === $this->currentPath) {
                continue;
            }
            $path = $dir . '/';
            $relativePath = substr($path, strlen($this->currentPath));
            $parts = explode('/', trim($relativePath, '/'));
            $current = &$tree;
            $fullPath = $this->currentPath;
            foreach ($parts as $part) {
                $fullPath .= $part . '/';
                if (!isset($current[$part])) {
                    $current[$part] = [
                        'name' => $part,
                        'path' => $fullPath,
                        'children' => [],
                    ];
                }
                $current = &$current[$part]['children'];
            }
        }
        return $tree;
    }

    private function getSSHConnection()
    {
        $ssh = new SSH2('127.0.0.1', 22);
        if (!$ssh->login('hmpanel', 'password')) {
            throw new \Exception('Login failed');
        }
        //run as root
        $ssh->exec('sudo -s');

        return $ssh;
    }

    public function openFileEditor($filePath)
    {
        $editableExtensions = ['.php', '.html', '.js', '.css', '.txt', '.env', '.json', '.xml', '.md', '.yml', '.yaml'];
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!in_array('.' . $extension, $editableExtensions)) {
            $this->dispatch('show-toast', ['message' => 'This file type is not editable.', 'type' => 'error']);
            return;
        }

        $ssh = $this->getSSHConnection();
        $content = $ssh->exec('cat ' . escapeshellarg($filePath));

        $this->editableFileContent = $content;
        $this->editingFilePath = $filePath;
        $this->showEditorModal = true;
    }

    public function saveFile()
    {
        $this->dispatch('getEditorContent');

        $ssh = $this->getSSHConnection();
        $tempFile = tempnam(sys_get_temp_dir(), 'ssh_edit_');
        file_put_contents($tempFile, $this->editableFileContent);

        $ssh->put($this->editingFilePath, file_get_contents($tempFile));
        unlink($tempFile);

        $this->showEditorModal = false;
        $this->dispatch('show-toast', ['message' => 'File saved successfully.', 'type' => 'success']);
    }

    public function updatedShowEditorModal()
    {
        if ($this->showEditorModal) {
            $this->dispatch('initializeAceEditor', $this->editableFileContent);
        } else {
            $this->editableFileContent = '';
            $this->editingFilePath = '';
        }
    }


    public function render()
    {
        return view('livewire.file-manager');
    }
}
