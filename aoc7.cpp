#include <vector>
#include <string>
#include <iostream>
#include <fstream>
using namespace std;

class FNode
{
private:
    bool isDir;
    string name;
    string path;
    FNode *parent;

    std::vector<int> fileSizes = std::vector<int>();
    std::vector<FNode *> children = std::vector<FNode *>();

public:
    FNode(bool isDir, string name, string path, FNode *parent = nullptr)
    {
        this->isDir = isDir;
        this->name = name;
        this->path = path;
        this->parent = parent;
        this->children = std::vector<FNode *>();
        this->fileSizes = std::vector<int>();
    }
    void registerFile(int size)
    {
        this->fileSizes.resize(this->fileSizes.size() + 1);
        this->fileSizes.push_back(size);
    }
    FNode *getParent()
    {
        return this->parent;
    }
    string getPath()
    {
        return this->path;
    }
    string getName()
    {
        return this->name;
    }
    size_t getDirSize()
    {
        return this->children.size();
    }
    void addChild(FNode *node)
    {
        this->children.resize(this->children.size() + 1);
        this->children.push_back(node);
    }

    int getSize()
    {
        size_t size;
        for (auto i : this->children)
        {
            size += i->getSize();
        }

        return size;
    }
};

int main()
{
    string dollar = "$";
    string cd = "cd";
    string dir = "dir";
    string ls = "ls";
    string rootPath = "/";
    string filename = "C:/Users/david/Documents/Projects/aoc22/questions/aoc7_input";
    ifstream File(filename);
    FNode *rootNode = nullptr;
    FNode *cursorNode;
    string cmd;
    bool lsCalled = false;
    bool lsMode = false;

    // Use a while loop together with the getline() function to read the file line by line
    int iter = 0;
    while (getline(File, cmd))
    {
        // if (iter++ > 50)
        //     break;

        bool isCommand = cmd.find(dollar) != string::npos;
        if (isCommand)
        {
            // Remove dollar
            cmd.erase(0, 2);

            // Check if move command
            if (cmd.find(cd) != string::npos)
            {
                lsMode = false;
                string name = cmd.substr(3, cmd.length() - 3);
                // If .. we are going up, no parsing required
                if (!name.compare(".."))
                {
                    cursorNode = cursorNode->getParent();
                    cout << ".. " << cursorNode->getPath() << endl;
                }
                else
                {
                    if (!name.compare(rootPath))
                    {
                        if (rootNode != nullptr)
                        {
                            perror("Root dir was already set");
                            return -1;
                        }

                        rootNode = new FNode(true, name, rootPath, nullptr);
                        cursorNode = rootNode;
                        lsCalled = false;
                    }
                    else
                    {
                        string newCursor = cursorNode->getPath() + name + "/";
                        if (!lsCalled)
                        {
                            perror("No LS was called for previous cursor");
                            return -1;
                        }

                        FNode *newNode = new FNode(true, name, newCursor, cursorNode);
                        cursorNode->addChild(newNode);
                        cout << "New child " << newNode->getPath() << " Size: " << newNode->getDirSize() << endl;
                        cursorNode = newNode;
                        lsCalled = false;
                    }
                    cout << "Dir: " << cursorNode->getPath() << endl;
                }
            }

            else if (cmd.find(ls) != string::npos)
            {
                // Dir listing is coming for current cursor
                lsMode = true;
                lsCalled = true;
            }
        }
        else
        {
            if (cmd.find(dir) != string::npos)
            {
                if (!lsMode)
                {
                    perror("No LS mode triggered for dir");
                    return -1;
                }
            }
            else
            {
                if (!lsMode)
                {
                    perror("No LS mode triggered for dir");
                    return -1;
                }

                char f[40];
                size_t i = 0;
                sscanf(cmd.c_str(), "%d %s", &i, f);
                cout << "Got size " << i << " file " << f << endl;
            }
        }
    }

    cout << "Program complete " << endl;
}