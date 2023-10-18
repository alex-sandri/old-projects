using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Tris
{
    class TrisGrid : Tris
    {
        private Label lblTurno;
        private Label lblVincitore;

        public TrisGrid(int dim, ref Panel pnlTavola, ref Label lblTurno, ref Label lblVincitore) : base(dim)
        {
            pnlTavola.Controls.Clear();
            pnlTavola.Enabled = true;

            this.lblTurno = lblTurno;
            this.lblVincitore = lblVincitore;

            this.lblVincitore.ResetText();

            lblTurno.Text = OttieniProssimoGiocatore() == 1 ? "X" : "O";

            for (int i = 0; i < Math.Pow(dim, 2); i++)
            {
                Button btnCella = new Button();

                // Separate per consentire il ridimensionamento a piacere, anche se non mantenendo l'aspect ratio
                btnCella.Width = pnlTavola.Width / dim;
                btnCella.Height = pnlTavola.Height / dim;

                                // Riga - Colonna
                btnCella.Name = $"{i / dim}-{i % dim}";

                btnCella.Top = pnlTavola.Height / dim * (int)(i / dim);
                btnCella.Left = pnlTavola.Width / dim * (i % dim);

                btnCella.Click += BtnCella_Click;

                pnlTavola.Controls.Add(btnCella);
            }
        }

        private void BtnCella_Click(object sender, EventArgs e)
        {
            Button btnCella = (Button)sender;

            if (btnCella.Text.Length == 0)
            {
                int x = Convert.ToInt32(btnCella.Name.Split('-')[0]);
                int y = Convert.ToInt32(btnCella.Name.Split('-')[1]);

                lblTurno.Text = OttieniProssimoGiocatore() == -1 ? "X" : "O";

                AggiornaTavola(x, y);

                btnCella.Text = OttieniStatoCasella(x, y) == -1 ? "X" : "O";

                int risultato = GiocoFinito(x, y);

                if (risultato == 1)
                    lblVincitore.Text = OttieniVincitore() == 1 ? "X" : "O";
                else if (risultato == -1) lblVincitore.Text = "Nessuno";

                if (risultato != 0) btnCella.Parent.Enabled = false;
            }
        }
    }
}
