using System;
using System.IO;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using Rifmoplet;

namespace RifmopletTest
{
    [TestClass]
    public class ProgramTest
    {
        [TestMethod]
        public void TestMain()
        {
            using (var stream = new StringWriter())
            {
                Console.SetOut(stream);

                Program.Main(new string[] { "всё", "переплетено" });

                Assert.AreEqual($"2{Environment.NewLine}10{Environment.NewLine}", stream.ToString());
            }
        }
    }
}
