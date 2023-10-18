using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Tris
{
    class Tris
    {
        private int[,] griglia;

        private int giocatore;
        private int nMosse = 0;
        private int dim;

        private bool finito = false;

        public Tris(int dim)
        {
            giocatore = -1; // giocatore X -> -1, giocatore O -> 1

            griglia = new int[dim, dim];

            this.dim = dim - 1;

            for (int i = 0; i < dim; i++)
            {
                for (int j = 0; j < dim; j++)
                {
                    griglia[i, j] = 0;
                }
            }
        }

        public int OttieniStatoCasella(int x, int y) => griglia[x, y];

        public int OttieniProssimoGiocatore() => -giocatore;

        /// <returns>Il giocatore che ha vinto se il gioco è finito, altrimenti 0</returns>
        public int OttieniVincitore() => finito ? giocatore : 0;

        public void AggiornaTavola(int x, int y)
        {
            griglia[x, y] = giocatore;

            giocatore = -giocatore;

            nMosse++;
        }

        /// <returns>1 se il gioco è finito e c'è un vincitore, 0 se non è finito, -1 se è finito senza vincitori</returns>
        public int GiocoFinito(int x, int y)
        {
            int risultato = 0;

            int tempX = x;
            int tempY = y;

            int temp = 0;

            // Controlla assi
            for (int i = 0; i <= dim; i++)
            {
                // -giocatore perchè il giocatore è cambiato in AggiornaTavola quindi bisogna controllare il giocatore che ha fatto la mossa, non quello attuale
                if (griglia[i, y] == -giocatore) temp++;
                else temp = 0;

                if (temp == 3 || (dim < 2 && temp == dim + 1)) risultato = 1;
            }

            temp = 0;

            for (int i = 0; i <= dim; i++)
            {
                if (griglia[x, i] == -giocatore) temp++;
                else temp = 0;

                if (temp == 3 || (dim < 2 && temp == dim + 1)) risultato = 1;
            }

            temp = 0;

            // Calcola cella di partenza per controllare la prima diagonale
            if (y - x >= 0)
            {
                y -= x;
                x = 0;
            }
            else
            {
                x -= y;
                y = 0;
            }

            // Controlla diagonali
            for (; x <= dim && y <= dim;)
            {
                if (griglia[x, y] == -giocatore) temp++;
                else temp = 0;

                if (temp == 3 || (dim < 2 && temp == dim + 1)) risultato = 1;

                x++;
                y++;
            }

            // Calcola cella di partenza per controllare la seconda diagonale
            // (sono state necessarie un po' tante ore per giungere a questa soluzione, soprattutto tante partite di prova e MessageBox di debug)
            // spero quindi che funzioni in ogni caso, però sono abbastanza fiducioso
            if (tempY != 0)
            {
                if (tempX != 0 && tempY != dim)
                {
                    if (tempX + tempY <= dim)
                    {
                        tempX += tempY;
                        tempY = 0;

                        temp = tempX;
                        tempX = tempY;
                        tempY = temp;
                    }
                    else
                    {
                        tempX = (tempX + tempY) - dim;
                        tempY = dim;
                    }
                }
            }
            else
            {
                temp = tempX;
                tempX = tempY;
                tempY = temp;
            }

            temp = 0;

            x = tempX;
            y = tempY;

            for (; x <= dim && y >= 0;)
            {
                if (griglia[x, y] == -giocatore) temp++;
                else temp = 0;

                if (temp == 3 || (dim < 2 && temp == dim + 1)) risultato = 1;

                x++;
                y--;
            }

            // Se tutte le caselle sono piene il gioco è finito
            if (nMosse == Math.Pow(dim + 1, 2) && risultato == 0) risultato = -1;

            if (risultato != 0) finito = true;

            return risultato;
        }
    }
}
