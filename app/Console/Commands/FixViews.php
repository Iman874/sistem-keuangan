<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix views to remove other_source references';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix views...');

        // Fix kasir income index view
        $kasirIncomeIndexPath = resource_path('views/kasir/income/index.blade.php');
        if (File::exists($kasirIncomeIndexPath)) {
            $content = File::get($kasirIncomeIndexPath);

            // Replace the other_source condition with simpler display
            $pattern = '/@if\(\$income->other_source\)[\s\S]+?@else[\s\S]+?@endif/';
            $replacement = '{{ $income->category->nama_pemasukkan ?? \'Kategori Dihapus\' }}
                                                                @if($income->description)
                                                                    <br><small>{{ $income->description }}</small>
                                                                @endif';

            $newContent = preg_replace($pattern, $replacement, $content);
            File::put($kasirIncomeIndexPath, $newContent);

            $this->info('Fixed kasir income index view');
        } else {
            $this->error('Kasir income index view not found');
        }

        // Fix owner kasir-income index view
        $ownerKasirIncomeIndexPath = resource_path('views/owner/kasir-income/index.blade.php');
        if (File::exists($ownerKasirIncomeIndexPath)) {
            $content = File::get($ownerKasirIncomeIndexPath);

            // Replace the other_source condition with simpler display
            $pattern = '/@if\(\$income->other_source\)[\s\S]+?@else[\s\S]+?@endif/';
            $replacement = '{{ $income->category->nama_pemasukkan ?? \'Kategori Dihapus\' }}
                                                                @if($income->description)
                                                                    <br><small>{{ $income->description }}</small>
                                                                @endif';

            $newContent = preg_replace($pattern, $replacement, $content);
            File::put($ownerKasirIncomeIndexPath, $newContent);

            $this->info('Fixed owner kasir-income index view');
        } else {
            $this->error('Owner kasir-income index view not found');
        }

        // Check for report view if it exists
        $reportViewPath = resource_path('views/owner/kasir-income/report.blade.php');
        if (File::exists($reportViewPath)) {
            $content = File::get($reportViewPath);

            // Replace the other_source condition with simpler display
            $pattern = '/@if\(\$income->other_source\)[\s\S]+?@else[\s\S]+?@endif/';
            $replacement = '{{ $income->category->nama_pemasukkan ?? \'Kategori Dihapus\' }}
                                @if($income->description)
                                    <br><small>{{ $income->description }}</small>
                                @endif';

            $newContent = preg_replace($pattern, $replacement, $content);
            File::put($reportViewPath, $newContent);

            $this->info('Fixed report view');
        }

        $this->info('All views have been fixed!');
    }
}
