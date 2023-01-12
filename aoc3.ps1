# lowercase is first compartment
# uppercase is second compartment

$filename = 'aoc3_input';

function Get-PrioValue {
    param (
        $Char
    )
    $value = [Byte][Char]$Char
    if ($value -gt 96) {
        return $value - 96
    }
    else {
        return $value - 38
    }
}

$total = 0
$lines = Get-Content $filename
foreach ($line in $lines) {
    $half = $line.Length / 2
    $first_part = $line.Substring(0, $half)
    $second_part = $line.Substring($half)
    
    $removed_chars = ""
    $duplicate_chars = ""
    foreach ($char in $first_part.ToCharArray()) {
        $index = $second_part.IndexOf($char)
        if ($index -eq -1) {
            $removed_chars += $char # Redundant
        }
        else {
            $duplicate_chars += $char
        }
    }
    $value = Get-PrioValue -Char $duplicate_chars[0]
    # $value
    $total += $value
}

# "Total:"
# $total

$line_count = $lines.Length
$line_count # 300, 100 groups
$total2 = 0
foreach ($index in 0..($line_count/3-1)) {
    $base = $index * 3
    $first_elf = $lines[$base]
    $second_elf = $lines[$base + 1]
    $third_elf = $lines[$base + 2]
    
    # $third_elf
    # $temp = $third_elf.Clone()
    # $temp.GetType()
    # $temp = $temp.Replace($third_elf[0], '', 0)
    # $temp
    # return

    foreach ($char in $third_elf.ToCharArray()) {
        $found_index = $first_elf.IndexOf($char)
        $found_index2 = $second_elf.IndexOf($char)
        If ($found_index -eq -1 -Or $found_index2 -eq -1) {
            # "Replacing"
            # $temp = $temp.Replace($char, '', 0)
        }
        else {
            $value = Get-PrioValue -Char $char
            "Line $base Found " + $char + " " + $value.ToString()
            $total2 += $value
            $first_elf
            $second_elf
            $third_elf
            break
        }
    }
    # $third_elf.Length - $temp.Length
}

$total2