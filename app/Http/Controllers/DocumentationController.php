<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DocumentationController extends Controller
{
    /**
     * Display the HubSpot setup guide
     */
    public function hubspotSetupGuide()
    {
        $content = File::get(base_path('docs/hubspot-setup-guide.md'));
        return view('documentation.show', [
            'title' => 'HubSpot Setup Guide - Kara',
            'content' => $this->markdownToHtml($content),
        ]);
    }

    /**
     * Display the Terms of Service
     */
    public function termsOfService()
    {
        $content = File::get(base_path('docs/terms-of-service.md'));
        return view('documentation.show', [
            'title' => 'Terms of Service - Kara',
            'content' => $this->markdownToHtml($content),
        ]);
    }

    /**
     * Display the Privacy Policy
     */
    public function privacyPolicy()
    {
        $content = File::get(base_path('docs/privacy-policy.md'));
        return view('documentation.show', [
            'title' => 'Privacy Policy - Kara',
            'content' => $this->markdownToHtml($content),
        ]);
    }

    /**
     * Display the Shared Data Documentation
     */
    public function sharedData()
    {
        try {
            $filePath = base_path('docs/shared-data.md');
            if (!File::exists($filePath)) {
                abort(404, 'Shared Data documentation file not found');
            }
            $content = File::get($filePath);
            return view('documentation.show', [
                'title' => 'Shared Data Documentation - Kara',
                'content' => $this->markdownToHtml($content),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading shared-data documentation: ' . $e->getMessage());
            abort(500, 'Error loading documentation: ' . $e->getMessage());
        }
    }

    /**
     * Display the Scope Justification Documentation
     */
    public function scopeJustification()
    {
        try {
            $filePath = base_path('docs/scope-justification.md');
            if (!File::exists($filePath)) {
                abort(404, 'Scope Justification documentation file not found');
            }
            $content = File::get($filePath);
            return view('documentation.show', [
                'title' => 'OAuth Scope Justification - Kara',
                'content' => $this->markdownToHtml($content),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading scope-justification documentation: ' . $e->getMessage());
            abort(500, 'Error loading documentation: ' . $e->getMessage());
        }
    }

    /**
     * Display the Security Policy
     */
    public function securityPolicy()
    {
        try {
            $filePath = base_path('docs/security.md');
            if (!File::exists($filePath)) {
                abort(404, 'Security Policy documentation file not found');
            }
            $content = File::get($filePath);
            return view('documentation.show', [
                'title' => 'Security Policy - Kara',
                'content' => $this->markdownToHtml($content),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading security policy documentation: ' . $e->getMessage());
            abort(500, 'Error loading documentation: ' . $e->getMessage());
        }
    }

    /**
     * Convert markdown to HTML
     * Simple markdown parser for basic formatting
     */
    protected function markdownToHtml($markdown)
    {
        $lines = explode("\n", $markdown);
        $html = [];
        $inCodeBlock = false;
        $inList = false;
        $inParagraph = false;
        $codeBlockContent = [];
        
        foreach ($lines as $line) {
            $originalLine = $line;
            $trimmed = trim($line);
            
            // Handle code blocks
            if (preg_match('/^```/', $trimmed)) {
                if ($inCodeBlock) {
                    // End code block
                    $html[] = '<pre><code>' . htmlspecialchars(implode("\n", $codeBlockContent)) . '</code></pre>';
                    $codeBlockContent = [];
                    $inCodeBlock = false;
                    $inParagraph = false;
                } else {
                    // Start code block
                    if ($inParagraph) {
                        $html[] = '</p>';
                        $inParagraph = false;
                    }
                    if ($inList) {
                        $html[] = '</ul>';
                        $inList = false;
                    }
                    $inCodeBlock = true;
                }
                continue;
            }
            
            if ($inCodeBlock) {
                $codeBlockContent[] = $originalLine;
                continue;
            }
            
            // Handle headers
            if (preg_match('/^(#{1,4})\s+(.+)$/', $trimmed, $matches)) {
                if ($inParagraph) {
                    $html[] = '</p>';
                    $inParagraph = false;
                }
                if ($inList) {
                    $html[] = '</ul>';
                    $inList = false;
                }
                $level = strlen($matches[1]);
                $text = $matches[2];
                $html[] = "<h{$level}>" . $this->processInlineMarkdown($text) . "</h{$level}>";
                continue;
            }
            
            // Handle lists
            if (preg_match('/^[-*]\s+(.+)$/', $trimmed, $matches) || preg_match('/^\d+\.\s+(.+)$/', $trimmed, $matches)) {
                if ($inParagraph) {
                    $html[] = '</p>';
                    $inParagraph = false;
                }
                if (!$inList) {
                    $html[] = '<ul>';
                    $inList = true;
                }
                $html[] = '<li>' . $this->processInlineMarkdown($matches[1]) . '</li>';
                continue;
            }
            
            // Handle empty lines
            if (empty($trimmed)) {
                if ($inParagraph) {
                    $html[] = '</p>';
                    $inParagraph = false;
                }
                if ($inList) {
                    $html[] = '</ul>';
                    $inList = false;
                }
                continue;
            }
            
            // Handle regular paragraphs
            if ($inList) {
                $html[] = '</ul>';
                $inList = false;
            }
            
            if (!$inParagraph) {
                $html[] = '<p>';
                $inParagraph = true;
            } else {
                $html[] = ' ';
            }
            
            $html[] = $this->processInlineMarkdown($trimmed);
        }
        
        // Close any open tags
        if ($inParagraph) {
            $html[] = '</p>';
        }
        if ($inList) {
            $html[] = '</ul>';
        }
        
        return implode('', $html);
    }
    
    /**
     * Process inline markdown (bold, italic, links, code)
     */
    protected function processInlineMarkdown($text)
    {
        // Use placeholders to protect HTML tags we're adding
        $placeholders = [];
        $placeholderIndex = 0;
        
        // Convert code spans first
        $text = preg_replace_callback('/`([^`]+)`/', function($matches) use (&$placeholders, &$placeholderIndex) {
            $placeholder = '___CODE_' . $placeholderIndex . '___';
            $placeholders[$placeholder] = '<code>' . htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8') . '</code>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Convert links [text](url)
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function($matches) use (&$placeholders, &$placeholderIndex) {
            $placeholder = '___LINK_' . $placeholderIndex . '___';
            $linkText = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
            $linkUrl = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');
            $placeholders[$placeholder] = '<a href="' . $linkUrl . '" target="_blank" rel="noopener noreferrer">' . $linkText . '</a>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Convert bold **text**
        $text = preg_replace_callback('/\*\*([^*]+)\*\*/', function($matches) use (&$placeholders, &$placeholderIndex) {
            $placeholder = '___BOLD_' . $placeholderIndex . '___';
            $placeholders[$placeholder] = '<strong>' . htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8') . '</strong>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Convert italic *text* (but not bold)
        $text = preg_replace_callback('/(?<!\*)\*([^*\s][^*]*[^*\s])\*(?!\*)/', function($matches) use (&$placeholders, &$placeholderIndex) {
            $placeholder = '___ITALIC_' . $placeholderIndex . '___';
            $placeholders[$placeholder] = '<em>' . htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8') . '</em>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Escape remaining HTML
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        // Restore placeholders
        foreach ($placeholders as $placeholder => $html) {
            $text = str_replace($placeholder, $html, $text);
        }
        
        return $text;
    }
}

