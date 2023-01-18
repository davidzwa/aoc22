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
    this->fileSizes.push_back(size);
  }
  FNode *getParent()
  {
    return this->parent;
  }
  FNode *getMinChildAboveSize(size_t threshold)
  {
    int min = -1;
    FNode *smallestChild = nullptr;
    for (auto child : this->children)
    {
      size_t size = child->getSize();
      FNode *lowerChild = child->getMinChildAboveSize(threshold);
      if (lowerChild)
      {
        size_t lowerSize = lowerChild->getSize();
        if (lowerSize < size && (lowerSize < min || min == -1) && lowerSize >= threshold)
        {
          min = lowerSize;
          smallestChild = lowerChild;
          cout << "New (lower level) min " << lowerSize / 1E3 << " path " << lowerChild->getPath() << endl;
          continue;
        }
      }

      cout << "Child size " << size / 1E3 << " path " << child->getPath() << endl;
      if ((size < min || min == -1) && size >= threshold)
      {
        min = size;
        smallestChild = child;
        cout << "New min " << size / 1E3 << " path " << child->getPath() << endl;
      }
    }

    if (smallestChild)
    {
      cout << "Smallest size " << smallestChild->getPath() << " size" << min << endl;
    }
    else
    {
      cout << "No child above threshold " << threshold / 1E3 << " path " << this->getPath() << " size " << getSize() / 1E3 << endl;
    }
    return smallestChild;
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
    this->children.push_back(node);
  }

  int getSize()
  {
    size_t size = 0;
    for (auto i : this->children)
    {
      size += i->getSize();
    }
    for (auto j : this->fileSizes)
    {
      size += j;
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
  const int totalDiskSize = 70E6;
  const int requiredFreeSize = 30E6;

  // Use a while loop together with the getline() function to read the file line by line
  int iter = 0;
  int64_t totalSize = 0;
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
          size_t size = cursorNode->getSize();
          bool isLess = size <= 100000;
          // cout << "$ cd .. , dir " << cursorNode->getPath() << " size " << size << " " << isLess << endl;
          if (isLess)
          {
            totalSize += size;
          }
          cursorNode = cursorNode->getParent();
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
            // cout << "New child " << newNode->getPath() << " Size: " << newNode->getDirSize() << endl;
            cursorNode = newNode;
            lsCalled = false;
          }
          // cout << "Dir: " << cursorNode->getPath() << endl;
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

        char fileName[40];
        size_t fileSize = 0;
        sscanf(cmd.c_str(), "%d %s", &fileSize, fileName);
        cursorNode->registerFile(fileSize);
        // cout << "Got size " << fileSize << " file " << fileName << " total: " << cursorNode->getSize() << endl;
      }
    }
  }

  size_t size = cursorNode->getSize();
  bool isLess = size <= 100000;
  cout << "$ completion , dir " << cursorNode->getPath() << " size " << size << " " << isLess << endl;
  if (isLess)
  {
    totalSize += size;
  }

  // 228918937 is too high
  // 1995759 is too low
  cout << "Calculation 1 complete " << totalSize << endl;

  size_t totalDiskUsage = rootNode->getSize();
  size_t currentFree = totalDiskSize - totalDiskUsage;
  size_t threshold = requiredFreeSize - currentFree;
  cout << "Usage " << totalDiskUsage / 1E3 << " Free " << currentFree / 1E3 << " needed free " << requiredFreeSize / 1E3 << endl;
  cout << "Removed min threshold: " << threshold / 1E3 << endl
       << endl;

  cursorNode = rootNode;
  size_t currentSize = 0;
  while (cursorNode != nullptr)
  {
    currentSize = cursorNode->getSize();
    FNode *newNode = cursorNode->getMinChildAboveSize(threshold);
    if (newNode)
    {
      cout << "Going down to " << newNode->getSize() / 1E3 << " path" << newNode->getPath() << endl;
    }
    cursorNode = newNode;
  }

  // 2654809 is too high
  cout << "Removed " << currentSize << endl;
}