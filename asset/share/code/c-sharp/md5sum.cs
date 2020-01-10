/*-----------------------------------------------------------------------------\
| Compute a file's MD5 hash.                                                   |
+---------+--------+----------+------------------------------------------------|
| @param  | string | filename | Path to file which will be hashed.             |
| @return | void   |          |                                                |
\---------+--------+----------+-----------------------------------------------*/
class MD5Example{
    public static string MD5Sum(string fileName){
        using(var md5 = System.Security.Cryptography.MD5.Create()){
            using(var stream = System.IO.File.OpenRead(fileName)){
                return System.BitConverter.ToString(md5.ComputeHash(stream)).Replace("-", System.String.Empty).ToLower();
            }
        }
    }

    // Example usage.
    public static void Main(string[] args){
        System.Console.WriteLine(MD5Sum(args[0]));
    }
}
