using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using LEMMATIZERLib;

namespace Rifmoplet
{
    public class Program
    {
        public static void Main(string[] args)
        {
            var lemmatizer = new LemmatizerRussian();
            var morph = new Morpher(lemmatizer);

            morph.Initialize();

            foreach (var word in args)
            {
                Console.WriteLine(morph.GetAccent(word));
            }
        }
    }
}
