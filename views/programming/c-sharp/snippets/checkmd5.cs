This is little snippet I have used when validating an important file copy. I compare the original checksum with the destination checksum, both returned from this code sample.

You will need to add System.IO, System.Text and System.Security.Cryptography to your using clause if you haven't done already.

To use, simply pass in a filename and the method will return an MD5 hash string.

    protected string GetMD5HashFromFile(string fileName)
    {
      using (var md5 = MD5.Create())
      {
        using (var stream = File.OpenRead(filename))
        {
          return BitConverter.ToString(md5.ComputeHash(stream)).Replace("-",string.Empty);
        }
      }
    }

You can also use this more advanced hashing function which you can pass in a HashAlgorithm object as a parameter to get a file has for different algorithms.

    public static class Algorithms
    {
      public static readonly HashAlgorithm MD5 = new MD5CryptoServiceProvider();
      public static readonly HashAlgorithm SHA1 = new SHA1Managed();
      public static readonly HashAlgorithm SHA256 = new SHA256Managed();
      public static readonly HashAlgorithm SHA384 = new SHA384Managed();
      public static readonly HashAlgorithm SHA512 = new SHA512Managed();
      public static readonly HashAlgorithm RIPEMD160 = new RIPEMD160Managed();
    }
     
    public static string GetHashFromFile(string fileName, HashAlgorithm algorithm)
    {
      using (var stream = new BufferedStream(File.OpenRead(fileName), 100000))
      {
        return BitConverter.ToString(algorithm.ComputeHash(stream)).Replace("-", string.Empty);
      }
    }

Usage is as follows:

    string checksumMd5 = GetChecksum(path, Algorithms.MD5);
    string checksumSha1 = GetChecksum(path, Algorithms.SHA1);
    string checksumSha256 = GetChecksum(path, Algorithms.SHA256);
    string checksumSha384 = GetChecksum(path, Algorithms.SHA384);
    string checksumSha512 = GetChecksum(path, Algorithms.SHA512);
    string checksumRipemd160 = GetChecksum(path, Algorithms.RIPEMD160);

