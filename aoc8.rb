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

    # iterate keys to analyze non-visible elements
    if row_index == 0 or row_index == rows -1 
        next
    end 

    left = nil
    right = nil
    for i in distr.keys.sort.reverse
        if i == 0
            next
        end

        # print i, " ", row_index, "\n"
        new_left = distr[i][0]
        new_right = distr[i].last

        # Exceptional case, no need to process
        if new_left == new_right
            left = right = new_right
            next
        end
        if left == nil or right == nil
            left = new_right
            right = new_right
        end
        
        if new_left < left
            # from new_left + 1 to left incl set to 0
            # print "Range left, ", new_left+1, " to ", left, "\n"
            for j in (new_left+1..(left-1))
                mv[row_index,j] = 0
            end
            left = new_left
        end
        # print_matrix(mv.row(row_index), cols)
        if new_right > right
            # print "Range right, ", right, " to ", new_right-1, "\n"
            for j in ((right+1)..(new_right-1))
                mv[row_index,j] = 0
            end
            right = new_right
        end
        # print_matrix(mv.row(row_index), cols)

        # print i, " ", , " ", , "\n"
    end
    # 
    # return
    # print max_e, " ", max_e_i, "\n"
    # print distr, "\n"
    
end

print_matrix(mv, cols)

rtups = Array.new(rows)
ctups = Array.new(cols)
# print(ctups.length)