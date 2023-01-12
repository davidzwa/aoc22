with open('questions/aoc1_input') as f:
    lines = f.readlines()

elf_values = []
max_elements = []
max_line = 0
max_value = 0
value = 0

elements = []
line_cnt = 0
for line in lines: 
    line_cnt += 1

    if line == '\n':
        max_elements = list(elements)
        elf_value = sum(max_elements)
        max_value = max(max_value, elf_value)
        elf_values.append(elf_value)
        value = 0
        max_line = line_cnt
        elements = []
    else:
        value = int(line)
        elements.append(value)
elf_values.sort(reverse=True)
print(elf_values[0:3], sum(elf_values[0:3]))

# 69003 is too low
# 72478 is correct