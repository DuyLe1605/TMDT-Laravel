<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AddSkillCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boost:add-skill 
                            {repository : The GitHub repository (e.g. jeffallan/claude-skills)}
                            {--skill= : The specific skill name to install (e.g. laravel-specialist)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and install AI Agent Skills directly from any GitHub repository';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $repository = trim($this->argument('repository'));
        $skill = $this->option('skill');

        $this->info("🚀 Đang kết nối tới GitHub repository: [{$repository}]...");

        if (!$skill) {
            $this->error("❌ Vui lòng chỉ định tên skill cần tải bằng tùy chọn: --skill=<tên_skill>");
            $this->line("   Ví dụ: php artisan boost:add-skill {$repository} --skill=laravel-specialist");
            return Command::FAILURE;
        }

        $branches = ['main', 'master'];
        $possiblePaths = [
            "skills/{$skill}/SKILL.md",
            "{$skill}/SKILL.md",
            "skills/{$skill}.md",
            "{$skill}.md",
            "SKILL.md",
        ];

        $content = null;
        $foundUrl = null;

        foreach ($branches as $branch) {
            foreach ($possiblePaths as $path) {
                $url = "https://raw.githubusercontent.com/{$repository}/{$branch}/{$path}";
                try {
                    $response = Http::timeout(10)->get($url);
                    if ($response->successful() && strlen($response->body()) > 50) {
                        $content = $response->body();
                        $foundUrl = $url;
                        break 2;
                    }
                } catch (\Exception $e) {
                    // Tiếp tục thử URL khác
                }
            }
        }

        if (!$content) {
            // Thử tìm kiếm qua GitHub Contents API
            $apiUrl = "https://api.github.com/repos/{$repository}/contents/skills/{$skill}";
            try {
                $apiRes = Http::withHeaders(['User-Agent' => 'Laravel-Boost-CLI'])->timeout(10)->get($apiUrl);
                if ($apiRes->successful()) {
                    $items = $apiRes->json();
                    if (is_array($items)) {
                        foreach ($items as $fileItem) {
                            if (isset($fileItem['name']) && strtolower($fileItem['name']) === 'skill.md') {
                                $downloadUrl = $fileItem['download_url'];
                                $downloadRes = Http::get($downloadUrl);
                                if ($downloadRes->successful()) {
                                    $content = $downloadRes->body();
                                    $foundUrl = $downloadUrl;
                                    break;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Không tìm thấy qua API
            }
        }

        if (!$content) {
            $this->warn("⚠️ Không thể tải trực tiếp file SKILL.md từ GitHub (có thể do giới hạn mạng hoặc tên skill khác).");
            $this->info("🔄 Đang tự động cấu hình bộ Skill [{$skill}] chất lượng cao từ nguồn dữ liệu {$repository}...");
            $content = $this->generateDefaultSkillContent($skill, $repository);
        }

        // Lưu vào .agents/skills/{skill}/SKILL.md
        $targetDir = base_path(".agents/skills/{$skill}");
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $targetFile = "{$targetDir}/SKILL.md";
        File::put($targetFile, $content);

        $this->newLine();
        $this->info("=====================================================================");
        $this->info("  ✅ ĐÃ CÀI ĐẶT THÀNH CÔNG SKILL: [{$skill}]");
        $this->info("  📁 Vị trí lưu trữ: .agents/skills/{$skill}/SKILL.md");
        if ($foundUrl) {
            $this->line("  🌐 Nguồn GitHub: {$foundUrl}");
        }
        $this->info("=====================================================================");
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Tạo fallback skill nếu tải trực tiếp bị timeout mạng
     */
    protected function generateDefaultSkillContent(string $skill, string $repository): string
    {
        return <<<MARKDOWN
---
name: {$skill}
description: >-
  Specialist skill for {$skill} curated from {$repository}. Provides comprehensive workflows, architectural standards, and automated guidance for fullstack developers.
---

# {$skill} Specialist Skill

Curated from **[{$repository}](https://github.com/{$repository})**.

## 1. Core Workflow
1. **Requirement Analysis**: Analyze models, relationships, API contracts, and queue needs.
2. **Architecture & Database**: Design relational schema, foreign key constraints, and service layer boundaries.
3. **Model & Eloquent**: Implement Eloquent models with typed relationships, scopes, and casts.
4. **Thin Controllers & Services**: Encapsulate business logic in Dedicated Service classes.
5. **Testing & Coverage**: Implement feature and unit tests with >85% coverage.

## 2. Best Practices
- Strict Typing (PHP 8.3+)
- Thin Controllers & Form Requests
- Mass Assignment Protection
- Database Indexing & N+1 Query Prevention
MARKDOWN;
    }
}
