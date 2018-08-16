using System;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using Rifmoplet;
using LEMMATIZERLib;

namespace RifmopletTest
{
    [TestClass]
    public class MorpherTest
    {
        [TestMethod]
        public void TestInitialize()
        {
            var lemmatizer = new LemmatizerRussian();
            var morph = new Morpher(lemmatizer);

            morph.Initialize();

            Assert.AreEqual(1, lemmatizer.UseStatistic);
        }

        [TestMethod]
        public void TestGetAccent()
        {
            var lemmatizer = new LemmatizerRussian();
            var morph = new Morpher(lemmatizer);
            morph.Initialize();

            var accent = morph.GetAccent("переплетено");

            Assert.AreEqual(10, accent);
        }
    }
}
