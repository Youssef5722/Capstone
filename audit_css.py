import os
import re

views_dir = r"f:\Corses\capstone management system\laravel\Capstone-main\resources\views"
output_file = r"f:\Corses\capstone management system\laravel\Capstone-main\PATCH_B_CSS_AUDIT.md"

problematic_regex = re.compile(
    r'(text-align\s*:\s*(left|right)|'
    r'margin-(left|right)\s*:|'
    r'padding-(left|right)\s*:|'
    r'\b(left|right)\s*:|'
    r'float\s*:\s*(left|right)|'
    r'border-(left|right)\s*:|'
    r'border-(top|bottom)-(left|right)-radius\s*:)',
    re.IGNORECASE
)

# Replacements mapping
def get_replacement(match_str):
    m = match_str.lower()
    if 'text-align' in m:
        return m.replace('left', 'start').replace('right', 'end')
    elif 'margin-left' in m: return m.replace('margin-left', 'margin-inline-start')
    elif 'margin-right' in m: return m.replace('margin-right', 'margin-inline-end')
    elif 'padding-left' in m: return m.replace('padding-left', 'padding-inline-start')
    elif 'padding-right' in m: return m.replace('padding-right', 'padding-inline-end')
    elif m.startswith('left') and ':' in m: return m.replace('left', 'inset-inline-start')
    elif m.startswith('right') and ':' in m: return m.replace('right', 'inset-inline-end')
    elif 'float' in m: return m.replace('left', 'inline-start').replace('right', 'inline-end')
    elif 'border-left' in m: return m.replace('border-left', 'border-inline-start')
    elif 'border-right' in m: return m.replace('border-right', 'border-inline-end')
    elif 'border-top-left-radius' in m: return m.replace('border-top-left-radius', 'border-start-start-radius')
    elif 'border-top-right-radius' in m: return m.replace('border-top-right-radius', 'border-start-end-radius')
    elif 'border-bottom-left-radius' in m: return m.replace('border-bottom-left-radius', 'border-end-start-radius')
    elif 'border-bottom-right-radius' in m: return m.replace('border-bottom-right-radius', 'border-end-end-radius')
    return 'use logical CSS equivalent'

results = []

for root, _, files in os.walk(views_dir):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            rel_path = os.path.relpath(filepath, views_dir).replace('\\', '/')
            
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
                
            # Find all <style> blocks
            # non-greedy match for <style>...</style>
            for style_match in re.finditer(r'<style[^>]*>(.*?)</style>', content, re.IGNORECASE | re.DOTALL):
                style_content = style_match.group(1)
                
                # Split by lines to track line numbers relative to the style block,
                # but to get the file line number we need to find where this style block starts.
                start_index = style_match.start()
                file_line_num_start = content.count('\n', 0, start_index) + 1
                
                lines = style_content.split('\n')
                current_selector = "Unknown Selector"
                
                for i, line in enumerate(lines):
                    line_stripped = line.strip()
                    
                    # Very naive CSS parser: if line has '{' and no ':', it's probably a selector
                    if '{' in line_stripped and not line_stripped.startswith('@'):
                        current_selector = line_stripped.split('{')[0].strip()
                        
                    # Also handle multi-line selectors, e.g. .class { \n prop: val \n }
                    # If line ends with '{', it's a selector
                    elif line_stripped.endswith('{'):
                        current_selector = line_stripped[:-1].strip()
                        
                    # Check for problematic properties
                    for prop_match in problematic_regex.finditer(line):
                        matched_str = prop_match.group(0).strip()
                        replacement = get_replacement(matched_str)
                        actual_line_num = file_line_num_start + i
                        
                        results.append({
                            'file': rel_path,
                            'class': current_selector,
                            'property': matched_str,
                            'replacement': replacement,
                            'line': actual_line_num
                        })

# Write markdown report
with open(output_file, 'w', encoding='utf-8') as f:
    f.write('# Patch B CSS Audit Report\n\n')
    f.write('**Date:** 2026-05-24\n')
    f.write('**Scope:** `<style>` blocks in `resources/views/**/*.blade.php`\n\n')
    
    if not results:
        f.write('✅ **No physical direction CSS properties found in `<style>` blocks.** All CSS uses logical properties or RTL-safe Bootstrap classes.\n')
    else:
        f.write('| File | Selector | Problematic Property | Suggested RTL-safe Replacement |\n')
        f.write('|---|---|---|---|\n')
        for r in results:
            f.write(f"| `{r['file']}:{r['line']}` | `{r['class']}` | `{r['property']}` | `{r['replacement']}` |\n")

print(f"Audit complete. Found {len(results)} issues. Written to {output_file}")
