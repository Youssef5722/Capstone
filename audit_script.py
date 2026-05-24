import os
import re

blade_dir = r"f:\Corses\capstone management system\laravel\Capstone-main\resources\views"

# Non-RTL aware Bootstrap classes (Bootstrap 3/4 legacy classes)
non_rtl_classes = ['ml-', 'mr-', 'pl-', 'pr-', 'text-left', 'text-right', 'float-left', 'float-right']

# We will look for lines containing standard english alphabet characters 
# outside of {{ }}, {!! !!}, @directive(), <tags>, and HTML attributes.
# This is tricky with regex, but we can do a naive check for common words or just check layouts manually.
# Since it's a CMS, there might be hardcoded text everywhere.

results = {
    "untranslated": [],
    "rtl_issues": []
}

for root, _, files in os.walk(blade_dir):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            rel_path = os.path.relpath(filepath, blade_dir)
            with open(filepath, 'r', encoding='utf-8') as f:
                lines = f.readlines()
                for i, line in enumerate(lines):
                    line_num = i + 1
                    
                    # Check for non-RTL classes
                    for cls in non_rtl_classes:
                        # naive check
                        if re.search(r'\b' + cls + r'\w*', line):
                            results['rtl_issues'].append(f"- {rel_path}:{line_num} -> contains `{cls}*`")
                            
                    # Check for hardcoded text inside HTML tags (rough heuristic)
                    # Strip out Blade {{ }} and {!! !!}
                    stripped_line = re.sub(r'\{\{.*?\}\}', '', line)
                    stripped_line = re.sub(r'\{!!.*?!!\}', '', stripped_line)
                    # Strip out blade directives @directive(...)
                    stripped_line = re.sub(r'@\w+(\(.*?\))?', '', stripped_line)
                    # Find text between > and <
                    text_matches = re.findall(r'>([^<]+)<', stripped_line)
                    for match in text_matches:
                        text = match.strip()
                        # If it contains alphabetical characters and is longer than 2 chars
                        if len(text) > 2 and re.search(r'[A-Za-z]', text):
                            # Exclude some obvious non-text (like &times; &copy;)
                            if not text.startswith('&'):
                                results['untranslated'].append(f"- {rel_path}:{line_num} -> '{text}'")
                                
                    # Also check for title="" or placeholder="" containing hardcoded text
                    attr_matches = re.findall(r'(title|placeholder)="([^"{}]+)"', stripped_line)
                    for attr, val in attr_matches:
                        if len(val) > 2 and re.search(r'[A-Za-z]', val):
                            results['untranslated'].append(f"- {rel_path}:{line_num} -> '{val}' (in {attr})")

print("=== NON-RTL CLASSES ===")
for r in results['rtl_issues']:
    print(r)

print("\n=== UNTRANSLATED STRINGS ===")
for r in results['untranslated']:
    print(r)
