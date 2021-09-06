CREATE TABLE timeline (
    TimelineId int NOT NULL AUTO_INCREMENT,
    MemoryType int NOT NULL,
    DateCreated datetime NOT NULL,
    DateModified datetime NOT NULL,
		EventDate date NOT NULL,
    EventTime time,
    EventTitle varchar(64) NOT NULL,
    EventDescription varchar(1000) NOT NULL,
    EventMedia varchar(255),
		EventYouTubeLink varchar(255),
		PRIMARY KEY (TimelineId)
);
