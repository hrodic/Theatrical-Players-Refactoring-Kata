namespace TheatricalPlayersRefactoringKata;

public class Performance(string playID, int audience)
{
    private string _playID = playID;
    private int _audience = audience;

    public string PlayID { get => _playID; set => _playID = value; }
    public int Audience { get => _audience; set => _audience = value; }
}
