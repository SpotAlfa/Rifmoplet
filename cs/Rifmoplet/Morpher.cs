using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using LEMMATIZERLib;

namespace Rifmoplet
{
    internal class Morpher
    {
        public const int NO_ACCENT = 255;

        private readonly ILemmatizer lemmatizer;

        public Morpher(ILemmatizer lemmatizer)
        {
            this.lemmatizer = lemmatizer;
        }

        public void Initialize()
        {
            lemmatizer.UseStatistic = 1;
            lemmatizer.MaximalPrediction = 1;

            lemmatizer.LoadStatisticRegistry(idlSubjectEnum.idlLiterature);
            lemmatizer.LoadDictionariesRegistry();
        }

        public int GetAccent(string word)
        {
            word = word.ToLower();

            if (word.IndexOf('ё') != -1)
            {
                return word.IndexOf('ё');
            }

            word = PrepareWord(word);

            var paradigms = lemmatizer.CreateParadigmCollectionFromForm(word, 0, 1);
            for (var i = 0; i < paradigms.Count; i++)
            {
                var paradigm = paradigms[i];
                for (uint j = 0; j < paradigm.Count; j++)
                {
                    if (word.Equals(paradigm[j]) && paradigm.Accent[j] != NO_ACCENT)
                    {
                        return paradigm.Accent[j];
                    }
                }
            }

            return NO_ACCENT;
        }

        private string _PrepareWord(string word)
        {
            var win1251 = Encoding.GetEncoding(1251);
            var win1252 = Encoding.GetEncoding(1252);

            word = word.ToUpper();

            return win1252.GetString(win1251.GetBytes(word));
        }

        private string PrepareWord(string word)
        {
            return word.ToUpper();
        }
    }
}
