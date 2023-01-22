require 'pathname'
require 'matrix'

def print_matrix(matrix, cols)
    matrix.each_with_index do |e, i, j|
        if e == nil
            print 'N'
        else
            print e # , i, " ", j, " "
        end
        if j == cols - 1
            print "\n"
        end
        # end
    end
    print "\n"
end

pn = Pathname.new("./questions/aoc8_input")
lines = File.readlines(pn)

rows = lines.length
cols = lines[0].length - 1 

# IDEA 1
# store matrix with heights
# store two vectors: row = (left,right) * rows, col = (top,bottom) * cols
# store matrix 'visible' 1's and 0's inited at Nil, except for outside row/col indices => set those to 1
# loop in row-mode and then in col-mode from number 9 to 0
# except for outside row/col indices:
# for each (row/col) find number extremes (left-right or top-bottom), mark all lower elements inside as blocked (0)

m = Matrix.build(rows, cols) {|row, col| nil }
mv = Matrix.build(rows, cols) {|row, col| 1 }
lines.each_with_index do |line, row_index|
    length = line.length
    line.strip.chars.each_with_index do |char, col_index|
        m[row_index, col_index] = char.to_i
    end

    # iterate keys to analyze non-visible elements
    if row_index == 0 or row_index == rows - 1
        next
    end 

    max_e = nil
    max_e_i = nil
    distr = {}

    # efficient dict for all numbers
    for elem, index in m.row(row_index).each_with_index
        if distr[elem] == nil 
            distr[elem] = [index]
        else
            distr[elem].push(index)
        end
        
        if max_e == nil or elem > max_e
            max_e = elem
            max_e_i = index
        end
    end

    left = nil
    right = nil
    for i in distr.keys.sort.reverse
        # print "->Row ", row_index, " Number ", i, "\n"
        new_left = distr[i][0]
        new_right = distr[i].last

        # print "Left ", left, " Right ", right, "\n"
        # print "Nleft ", new_left, " NRight ", new_right, "\n"
        # print "new Left ", new_left, " new_right ", new_right, "\n"
        
        if left == nil or right == nil
            left = new_left
            right = new_right
            # print "Nilling", " Left ", left, " Right ", right, "\n"
            for j in (new_left+1..(new_right-1))
                mv[row_index,j] = 0
            end
            # print row_index, " Vec "
            # print_matrix(mv.row(row_index), cols)
            next
        end

        if new_left < left
            # print "Lefting\n"
            # print "Printing ", new_left+1,  " to ", left-1, "\n"
            for j in (new_left+1..(left-1))
                mv[row_index,j] = 0
            end
            # print row_index, " Vec "
            # print_matrix(mv.row(row_index), cols)
            left = new_left
        end
        
        if new_right > right
            # print "Righting ", row_index, " Zeroing ", right+1,  " to ", new_right-1, "\n"
            for j in ((right+1)..(new_right-1))
                mv[row_index,j] = 0
            end
            # print row_index, " Vec "
            # print_matrix(mv.row(row_index), cols)
            right = new_right
        end
    end
    # print_matrix(mv.row(row_index), cols)
end

visible_tree_count = 0
for col_index in (0..(cols-1))
    
    max = m.column(col_index).first
    for elem, index in m.column(col_index).each_with_index
        # If its already marked 1, no need to mark that element
        # if mv[index,col_index] == 1
        #     if elem > max 
        #         max = elem
        #     end
        #     next
        if elem > max
            mv[index, col_index] = 1
            max = elem

            # if mv[98-6, 2] == 1
            #     raise "Wrong entry row 7 col 2 (==1) elem:#{elem} max:#{max} row:#{index} col:#{col_index}"
            # end
        end
    end

    max2 = m.column(col_index).to_a.last()
    for elem, i in m.column(col_index).to_a.reverse.each_with_index
        r_index = rows - 1 - i
        # mv_elem = mv[index,col_index]
        # if mv_elem == 1
        #     if elem > max 
        #         max2 = elem
        #     end
        #     next
        # end

        if elem > max2
            # if mv[6, 2] == 1
            #     raise "(Pre-Reverse) Wrong entry row 7 col 2 (==1)  elem:#{elem} max:#{max2} row:#{index} col:#{col_index}"
            # end
            if r_index == 6 and col_index == 2 
                raise "max2:#{max2} elem: #{elem}"
            end
            mv[r_index, col_index] = 1
            max2 = elem
                        
            if mv[6, 2] == 1
                raise "(Reverse) Wrong entry row 7 col 2 (==1)  elem:#{elem} max:#{max2} row:#{r_index} col:#{col_index}"
            end
        end
    end

    # if col_index == 2
        # print_matrix(mv.column(col_index), rows)
    # end
end

if mv[6, 2] == 1
    raise "Wrong entry row 7 col 2 (==1)"
end

# print_matrix(m, cols)
print_matrix(mv, cols)

# 689 is too low
# 776 is too low
# 778 is too low
# 1320 is not correct
# 1308 is not correct (v3 row fixes) 
# 1943
puts mv.count(1)