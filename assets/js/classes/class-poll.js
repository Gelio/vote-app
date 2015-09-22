/*
votes: [
    {name: 'option 1', amount: 20},
    {name: 'option 2', amount: 50}
]
 */

function Poll(name, votes) {
    this.name = name;
    this.votes = votes;
    this.totalVotes = 0;
    var self = this;

    votes.forEach(function(vote) {
        self.totalVotes += vote.amount;
    });


    this.data = this.votes.map(function(option) { return option.amount});
    this.labels = this.votes.map(function(option) { return option.name});
}

Poll.prototype.getName = function() {
    return this.name;
};