<?php
// The list of skills people can pick from (used in register, edit profile, add post and the search boxes).
// To add a new skill, just write it inside one of the groups below.
$SKILL_GROUPS = [
    'Development' => [
        'Web Development', 'Frontend', 'Backend', 'Full Stack', 'Mobile App Development',
        'Android', 'iOS', 'Server-side Scripting', 'Client-side Scripting', 'API Development',
        'WordPress / CMS', 'Game Development', 'Desktop Applications',
    ],
    'Languages & Frameworks' => [
        'HTML / CSS', 'JavaScript', 'TypeScript', 'PHP', 'Python', 'Java', 'C / C++', 'C#',
        'Node.js', 'React', 'Angular', 'Vue.js', 'Laravel', 'Django', 'Flutter', 'Kotlin', 'Swift',
    ],
    'Data & Database' => [
        'Database', 'MySQL', 'MongoDB', 'PostgreSQL', 'Data Analysis', 'Data Science',
        'Machine Learning', 'Artificial Intelligence', 'Data Visualization', 'Big Data',
    ],
    'Infrastructure & Security' => [
        'Networking', 'Cloud Computing', 'DevOps', 'Server Administration', 'Linux',
        'Cybersecurity', 'Ethical Hacking', 'Cryptography', 'Blockchain', 'IoT / Embedded Systems',
    ],
    'Engineering Practices' => [
        'System Design', 'Software Architecture', 'Debugging', 'Testing / QA', 'Version Control (Git)',
        'Documentation', 'Code Review', 'Performance Optimization', 'Project Management', 'Agile / Scrum',
    ],
    'Design' => [
        'UI Design', 'UX Research', 'Graphic Design', 'Logo & Branding', 'Video Editing',
        'Animation', '3D Modeling', 'Photography',
    ],
    'Business & Marketing' => [
        'Product Management', 'Business Development', 'Digital Marketing', 'SEO', 'Social Media',
        'Content Writing', 'Market Research', 'Sales', 'Finance & Accounting', 'Legal & Compliance',
        'Pitching & Public Speaking', 'Customer Support',
    ],
];

// lowercase name => proper name (used to check what was posted)
$SKILL_MAP = [];
foreach ($SKILL_GROUPS as $list) {
    foreach ($list as $s) {
        $SKILL_MAP[strtolower($s)] = $s;
    }
}

function skill_known($name) {
    global $SKILL_MAP;
    return isset($SKILL_MAP[strtolower(trim($name))]);
}

// Reads the ticked skills + the "other skills" box from the form and returns a clean array.
function collect_skills($arrayKey, $otherKey) {
    global $SKILL_MAP;
    $out = [];

    $picked = $_POST[$arrayKey] ?? [];
    if (!is_array($picked)) $picked = [];
    foreach ($picked as $s) {
        $k = strtolower(trim($s));
        if (isset($SKILL_MAP[$k])) $out[$k] = $SKILL_MAP[$k];
    }

    foreach (explode(',', $_POST[$otherKey] ?? '') as $s) {
        // keep at most 30 characters (works with any language, no extra PHP extension needed)
        preg_match('/^.{0,30}/us', trim($s), $m);
        $s = trim($m[0] ?? '');
        if ($s !== '' && !isset($out[strtolower($s)])) $out[strtolower($s)] = $s;
    }
    return array_values($out);
}

// skills are saved in one column as "PHP, Database, Networking"
function skills_to_text($arr) {
    return implode(', ', $arr);
}

// returns an error text if the skills are missing or too long for the column, otherwise ''
function skills_error($arr) {
    if (count($arr) == 0) return "Please select at least one skill.";
    if (strlen(skills_to_text($arr)) > 250) return "You selected too many skills. Please keep the most important ones (about 15).";
    return '';
}

// the chip picker: search box, groups of skills (tick as many as you like) and an "other" box
function skill_picker($name, $selected = [], $other = '', $otherName = 'skills_other') {
    global $SKILL_GROUPS;
    $sel = array_map('strtolower', $selected);

    echo '<div class="skillpick">';
    echo '<div class="skillpick-top">';
    echo '<input type="text" class="skillpick-search" placeholder="Search skills..." autocomplete="off">';
    echo '<span class="skillpick-count">0 selected</span>';
    echo '</div>';
    echo '<div class="skillpick-list">';
    foreach ($SKILL_GROUPS as $group => $list) {
        echo '<div class="skillpick-group"><div class="skillpick-title">' . e($group) . '</div><div class="skillpick-chips">';
        foreach ($list as $s) {
            $checked = in_array(strtolower($s), $sel) ? ' checked' : '';
            echo '<label class="chip"><input type="checkbox" name="' . e($name) . '[]" value="' . e($s) . '"' . $checked . '><span>' . e($s) . '</span></label>';
        }
        echo '</div></div>';
    }
    echo '<div class="skillpick-empty" style="display:none">No skill found. Add it in the box below.</div>';
    echo '</div>';
    echo '<input type="text" class="skillpick-other" name="' . e($otherName) . '" placeholder="Other skills not in the list (separate with commas)" value="' . e($other) . '">';
    echo '</div>';
}

// dropdown with every skill, used to search posts / people by skill
function skill_select($name, $current = '', $placeholder = 'Any skill') {
    global $SKILL_GROUPS;
    echo '<select name="' . e($name) . '">';
    echo '<option value="">' . e($placeholder) . '</option>';
    if ($current != '' && !skill_known($current)) {
        echo '<option value="' . e($current) . '" selected>' . e($current) . '</option>';
    }
    foreach ($SKILL_GROUPS as $group => $list) {
        echo '<optgroup label="' . e($group) . '">';
        foreach ($list as $s) {
            $sel = (strtolower($s) == strtolower($current)) ? ' selected' : '';
            echo '<option value="' . e($s) . '"' . $sel . '>' . e($s) . '</option>';
        }
        echo '</optgroup>';
    }
    echo '</select>';
}
