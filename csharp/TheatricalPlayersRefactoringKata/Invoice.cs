using System.Collections.Generic;

namespace TheatricalPlayersRefactoringKata;

public class Invoice(string customer, List<Performance> performance)
{
    private string _customer = customer;
    private List<Performance> _performances = performance;

    public string Customer { get => _customer; set => _customer = value; }
    public List<Performance> Performances { get => _performances; set => _performances = value; }
}
