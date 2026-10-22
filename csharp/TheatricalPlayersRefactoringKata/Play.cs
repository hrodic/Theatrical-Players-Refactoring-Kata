namespace TheatricalPlayersRefactoringKata;

public class Play(string name, string type)
{
    private string _name = name;
    private string _type = type;

    public string Name { get => _name; set => _name = value; }
    public string Type { get => _type; set => _type = value; }
}
